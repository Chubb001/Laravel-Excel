<?php

namespace Chubb001\Excel31\Tests;

use Illuminate\Support\Facades\Queue;
use Chubb001\Excel31\Jobs\ReadChunk;
use Illuminate\Queue\Events\JobProcessing;
use Chubb001\Excel31\Concerns\Importable;
use Chubb001\Excel31\Files\TemporaryFile;
use Illuminate\Foundation\Bus\PendingDispatch;
use Chubb001\Excel31\Files\RemoteTemporaryFile;
use Chubb001\Excel31\Tests\Data\Stubs\QueuedImport;
use Chubb001\Excel31\Tests\Data\Stubs\AfterQueueImportJob;

class QueuedImportTest extends TestCase
{
    /**
     * Setup the test environment.
     */
    protected function setUp(): void
    {
        parent::setUp();

        $this->loadLaravelMigrations(['--database' => 'testing']);
        $this->loadMigrationsFrom(__DIR__ . '/Data/Stubs/Database/Migrations');
    }

    /**
     * @test
     */
    public function cannot_queue_import_that_does_not_implement_should_queue()
    {
        $this->expectException(\InvalidArgumentException::class);
        $this->expectExceptionMessage('Importable should implement ShouldQueue to be queued.');

        $import = new class {
            use Importable;
        };

        $import->queue('import-batches.xlsx');
    }

    /**
     * @test
     */
    public function can_queue_an_import()
    {
        $import = new QueuedImport();

        $chain = $import->queue('import-batches.xlsx')->chain([
            new AfterQueueImportJob(5000),
        ]);

        $this->assertInstanceOf(PendingDispatch::class, $chain);
    }

    /**
     * @test
     */
    public function can_queue_import_with_remote_temp_disk()
    {
        config()->set('excel.temporary_files.remote_disk', 'test');

        // Delete the local temp file before each read chunk job
        // to simulate using a shared remote disk, without
        // having a dependency on a local temp file.
        Queue::before(function (JobProcessing $event) {
            if ($event->job->resolveName() === ReadChunk::class) {
                /** @var TemporaryFile $tempFile */
                $tempFile = $this->inspectJobProperty($event->job, 'temporaryFile');

                $this->assertInstanceOf(RemoteTemporaryFile::class, $tempFile);

                // Should exist remote
                $this->assertTrue(
                    $tempFile->exists()
                );

                $this->assertTrue(
                    unlink($tempFile->getLocalPath())
                );
            }
        });

        $import = new QueuedImport();

        $chain = $import->queue('import-batches.xlsx')->chain([
            new AfterQueueImportJob(5000),
        ]);

        $this->assertInstanceOf(PendingDispatch::class, $chain);
    }
}
