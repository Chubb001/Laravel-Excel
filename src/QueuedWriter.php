<?php

namespace Chubb001\Excel31;

use Traversable;
use Illuminate\Support\Collection;
use Chubb001\Excel31\Jobs\CloseSheet;
use Chubb001\Excel31\Jobs\QueueExport;
use Chubb001\Excel31\Concerns\FromView;
use Chubb001\Excel31\Concerns\FromQuery;
use Chubb001\Excel31\Files\TemporaryFile;
use Chubb001\Excel31\Jobs\SerializedQuery;
use Chubb001\Excel31\Jobs\AppendDataToSheet;
use Chubb001\Excel31\Jobs\AppendViewToSheet;
use Chubb001\Excel31\Jobs\StoreQueuedExport;
use Chubb001\Excel31\Concerns\FromCollection;
use Chubb001\Excel31\Jobs\AppendQueryToSheet;
use Chubb001\Excel31\Files\TemporaryFileFactory;
use Chubb001\Excel31\Concerns\WithMultipleSheets;
use Chubb001\Excel31\Concerns\WithCustomChunkSize;
use Chubb001\Excel31\Concerns\WithCustomQuerySize;

class QueuedWriter
{
    /**
     * @var Writer
     */
    protected $writer;

    /**
     * @var int
     */
    protected $chunkSize;

    /**
     * @var TemporaryFileFactory
     */
    protected $temporaryFileFactory;

    /**
     * @param Writer               $writer
     * @param TemporaryFileFactory $temporaryFileFactory
     */
    public function __construct(Writer $writer, TemporaryFileFactory $temporaryFileFactory)
    {
        $this->writer               = $writer;
        $this->chunkSize            = config('excel.exports.chunk_size', 1000);
        $this->temporaryFileFactory = $temporaryFileFactory;
    }

    /**
     * @param object       $export
     * @param string       $filePath
     * @param string       $disk
     * @param string|null  $writerType
     * @param array|string $diskOptions
     *
     * @return \Illuminate\Foundation\Bus\PendingDispatch
     */
    public function store($export, string $filePath, string $disk = null, string $writerType = null, $diskOptions = [])
    {
        $temporaryFile = $this->temporaryFileFactory->make();

        $jobs = $this->buildExportJobs($export, $temporaryFile, $writerType);

        $jobs->push(new StoreQueuedExport(
            $temporaryFile,
            $filePath,
            $disk,
            $diskOptions
        ));

        return QueueExport::withChain($jobs->toArray())->dispatch($export, $temporaryFile, $writerType);
    }

    /**
     * @param object        $export
     * @param TemporaryFile $temporaryFile
     * @param string        $writerType
     *
     * @return Collection
     */
    private function buildExportJobs($export, TemporaryFile $temporaryFile, string $writerType): Collection
    {
        $sheetExports = [$export];
        if ($export instanceof WithMultipleSheets) {
            $sheetExports = $export->sheets();
        }

        $jobs = new Collection;
        foreach ($sheetExports as $sheetIndex => $sheetExport) {
            if ($sheetExport instanceof FromCollection) {
                $jobs = $jobs->merge($this->exportCollection($sheetExport, $temporaryFile, $writerType, $sheetIndex));
            } elseif ($sheetExport instanceof FromQuery) {
                $jobs = $jobs->merge($this->exportQuery($sheetExport, $temporaryFile, $writerType, $sheetIndex));
            } elseif ($sheetExport instanceof FromView) {
                $jobs = $jobs->merge($this->exportView($sheetExport, $temporaryFile, $writerType, $sheetIndex));
            }

            $jobs->push(new CloseSheet($sheetExport, $temporaryFile, $writerType, $sheetIndex));
        }

        return $jobs;
    }

    /**
     * @param FromCollection $export
     * @param TemporaryFile  $temporaryFile
     * @param string         $writerType
     * @param int            $sheetIndex
     *
     * @return Collection
     */
    private function exportCollection(
        FromCollection $export,
        TemporaryFile $temporaryFile,
        string $writerType,
        int $sheetIndex
    ): Collection {
        return $export
            ->collection()
            ->chunk($this->getChunkSize($export))
            ->map(function ($rows) use ($writerType, $temporaryFile, $sheetIndex, $export) {
                if ($rows instanceof Traversable) {
                    $rows = iterator_to_array($rows);
                }

                return new AppendDataToSheet(
                    $export,
                    $temporaryFile,
                    $writerType,
                    $sheetIndex,
                    $rows
                );
            });
    }

    /**
     * @param FromQuery     $export
     * @param TemporaryFile $temporaryFile
     * @param string        $writerType
     * @param int           $sheetIndex
     *
     * @return Collection
     */
    private function exportQuery(
        FromQuery $export,
        TemporaryFile $temporaryFile,
        string $writerType,
        int $sheetIndex
    ): Collection {
        $query = $export->query();

        $count = $export instanceof WithCustomQuerySize ? $export->querySize() : $query->count();
        $spins = ceil($count / $this->getChunkSize($export));

        $jobs = new Collection();

        for ($page = 1; $page <= $spins; $page++) {
            $serializedQuery = new SerializedQuery(
                $query->forPage($page, $this->getChunkSize($export))
            );

            $jobs->push(new AppendQueryToSheet(
                $export,
                $temporaryFile,
                $writerType,
                $sheetIndex,
                $serializedQuery
            ));
        }

        return $jobs;
    }

    /**
     * @param FromView     $export
     * @param TemporaryFile $temporaryFile
     * @param string        $writerType
     * @param int           $sheetIndex
     *
     * @return Collection
     */
    private function exportView(
        FromView $export,
        TemporaryFile $temporaryFile,
        string $writerType,
        int $sheetIndex
    ): Collection {
        $jobs = new Collection();
        $jobs->push(new AppendViewToSheet(
            $export,
            $temporaryFile,
            $writerType,
            $sheetIndex
        ));

        return $jobs;
    }

    /**
     * @param object|WithCustomChunkSize $export
     *
     * @return int
     */
    private function getChunkSize($export): int
    {
        if ($export instanceof WithCustomChunkSize) {
            return $export->chunkSize();
        }

        return $this->chunkSize;
    }
}
