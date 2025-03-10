<?php

namespace Chubb001\Excel31\Tests\Concerns;

use PHPUnit\Framework\Assert;
use Chubb001\Excel31\Tests\TestCase;
use Chubb001\Excel31\Concerns\ToArray;
use Chubb001\Excel31\Concerns\Importable;

class ToArrayTest extends TestCase
{
    /**
     * @test
     */
    public function can_import_to_array()
    {
        $import = new class implements ToArray {
            use Importable;

            public $called = false;

            /**
             * @param array $array
             */
            public function array(array $array)
            {
                $this->called = true;

                Assert::assertEquals([
                    ['test', 'test'],
                    ['test', 'test'],
                ], $array);
            }
        };

        $import->import('import.xlsx');

        $this->assertTrue($import->called);
    }

    /**
     * @test
     */
    public function can_import_multiple_sheets_to_array()
    {
        $import = new class implements ToArray {
            use Importable;

            public $called = 0;

            /**
             * @param array $array
             */
            public function array(array $array)
            {
                $this->called++;

                $sheetNumber = $this->called;

                Assert::assertEquals([
                    [$sheetNumber . '.A1', $sheetNumber . '.B1'],
                    [$sheetNumber . '.A2', $sheetNumber . '.B2'],
                ], $array);
            }
        };

        $import->import('import-multiple-sheets.xlsx');

        $this->assertEquals(2, $import->called);
    }
}
