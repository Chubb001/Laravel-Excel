<?php

namespace Chubb001\Excel31\Tests\Concerns;

use Chubb001\Excel31\Tests\TestCase;
use Chubb001\Excel31\Tests\Data\Stubs\WithTitleExport;

class WithTitleTest extends TestCase
{
    /**
     * @test
     */
    public function can_export_with_title()
    {
        $export = new WithTitleExport();

        $response = $export->store('with-title-store.xlsx');

        $this->assertTrue($response);

        $spreadsheet = $this->read(__DIR__ . '/../Data/Disks/Local/with-title-store.xlsx', 'Xlsx');

        $this->assertEquals('given-title', $spreadsheet->getProperties()->getTitle());
        $this->assertEquals('given-title', $spreadsheet->getActiveSheet()->getTitle());
    }
}
