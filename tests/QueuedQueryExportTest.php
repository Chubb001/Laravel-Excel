<?php

namespace Chubb001\Excel31\Tests;

use Chubb001\Excel31\Tests\Data\Stubs\Database\User;
use Chubb001\Excel31\Tests\Data\Stubs\AfterQueueExportJob;
use Chubb001\Excel31\Tests\Data\Stubs\FromUsersQueryExport;
use Chubb001\Excel31\Tests\Data\Stubs\FromUsersQueryExportWithMapping;

class QueuedQueryExportTest extends TestCase
{
    /**
     * Setup the test environment.
     */
    protected function setUp(): void
    {
        parent::setUp();

        $this->loadLaravelMigrations(['--database' => 'testing']);
        $this->withFactories(__DIR__ . '/Data/Stubs/Database/Factories');

        factory(User::class)->times(100)->create([]);
    }

    /**
     * @test
     */
    public function can_queue_an_export()
    {
        $export = new FromUsersQueryExport();

        $export->queue('queued-query-export.xlsx')->chain([
            new AfterQueueExportJob(__DIR__ . '/Data/Disks/Local/queued-query-export.xlsx'),
        ]);

        $actual = $this->readAsArray(__DIR__ . '/Data/Disks/Local/queued-query-export.xlsx', 'Xlsx');

        $this->assertCount(100, $actual);

        // 6 of the 7 columns in export, excluding the "hidden" password column.
        $this->assertCount(6, $actual[0]);
    }

    /**
     * @test
     */
    public function can_queue_an_export_with_mapping()
    {
        $export = new FromUsersQueryExportWithMapping();

        $export->queue('queued-query-export-with-mapping.xlsx')->chain([
            new AfterQueueExportJob(__DIR__ . '/Data/Disks/Local/queued-query-export-with-mapping.xlsx'),
        ]);

        $actual = $this->readAsArray(__DIR__ . '/Data/Disks/Local/queued-query-export-with-mapping.xlsx', 'Xlsx');

        $this->assertCount(100, $actual);

        // Only 1 column when using map()
        $this->assertCount(1, $actual[0]);
        $this->assertEquals(User::value('name'), $actual[0][0]);
    }
}
