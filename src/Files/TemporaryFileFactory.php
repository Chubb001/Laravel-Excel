<?php

namespace Chubb001\Excel31\Files;

use Illuminate\Support\Str;

class TemporaryFileFactory
{
    /**
     * @var string|null
     */
    private $temporaryPath;

    /**
     * @var string|null
     */
    private $temporaryDisk;

    /**
     * @param string|null $temporaryPath
     * @param string|null $temporaryDisk
     */
    public function __construct(string $temporaryPath = null, string $temporaryDisk = null)
    {
        $this->temporaryPath = $temporaryPath;
        $this->temporaryDisk = $temporaryDisk;
    }

    /**
     * @param string|null $fileExtension
     *
     * @return TemporaryFile
     */
    public function make(string $fileExtension = null): TemporaryFile
    {
        if (null !== $this->temporaryDisk) {
            return $this->makeRemote();
        }

        return $this->makeLocal(null, $fileExtension);
    }

    /**
     * @param string|null $fileName
     *
     * @param string|null $fileExtension
     *
     * @return LocalTemporaryFile
     */
    public function makeLocal(string $fileName = null, string $fileExtension = null): LocalTemporaryFile
    {
        if (!file_exists($this->temporaryPath) && !mkdir($concurrentDirectory = $this->temporaryPath) && !is_dir($concurrentDirectory)) {
            throw new \RuntimeException(sprintf('Directory "%s" was not created', $concurrentDirectory));
        }

        return new LocalTemporaryFile(
            $this->temporaryPath . DIRECTORY_SEPARATOR . ($fileName ?: $this->generateFilename($fileExtension))
        );
    }

    /**
     * @return RemoteTemporaryFile
     */
    private function makeRemote(): RemoteTemporaryFile
    {
        $filename = $this->generateFilename();

        return new RemoteTemporaryFile(
            $this->temporaryDisk,
            $filename,
            $this->makeLocal($filename)
        );
    }

    /**
     * @param string|null $fileExtension
     *
     * @return string
     */
    private function generateFilename(string $fileExtension = null): string
    {
        return 'laravel-excel-' . Str::random(32) . ($fileExtension ? '.' . $fileExtension : '');
    }
}
