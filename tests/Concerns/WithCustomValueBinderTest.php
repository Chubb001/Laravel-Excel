<?php

namespace Chubb001\Excel31\Tests\Concerns;

use Carbon\Carbon;
use Chubb001\Excel31\Excel;
use PHPUnit\Framework\Assert;
use Illuminate\Support\Collection;
use Chubb001\Excel31\Tests\TestCase;
use Chubb001\Excel31\Concerns\ToArray;
use PhpOffice\PhpSpreadsheet\Cell\Cell;
use PhpOffice\PhpSpreadsheet\Shared\Date;
use Chubb001\Excel31\Concerns\Exportable;
use PhpOffice\PhpSpreadsheet\Cell\DataType;
use Chubb001\Excel31\Concerns\FromCollection;
use PhpOffice\PhpSpreadsheet\Style\NumberFormat;
use Chubb001\Excel31\Concerns\WithCustomValueBinder;
use PhpOffice\PhpSpreadsheet\Cell\DefaultValueBinder;

class WithCustomValueBinderTest extends TestCase
{
    /**
     * @test
     */
    public function can_set_a_value_binder_on_export()
    {
        Carbon::setTestNow(new Carbon('2018-08-07 18:00:00'));

        $export = new class extends DefaultValueBinder implements FromCollection, WithCustomValueBinder {
            use Exportable;

            /**
             * @return Collection
             */
            public function collection()
            {
                return collect([
                    [Carbon::now(), '10%'],
                ]);
            }

            /**
             * {@inheritdoc}
             */
            public function bindValue(Cell $cell, $value)
            {
                // Handle percentage
                if (preg_match('/^\-?\d*\.?\d*\s?\%$/', $value)) {
                    $cell->setValueExplicit(
                        (float) str_replace('%', '', $value) / 100,
                        DataType::TYPE_NUMERIC
                    );

                    $cell
                        ->getWorksheet()
                        ->getStyle($cell->getCoordinate())
                        ->getNumberFormat()
                        ->setFormatCode(NumberFormat::FORMAT_PERCENTAGE_00);

                    return true;
                }

                // Handle Carbon dates
                if ($value instanceof Carbon) {
                    $cell->setValueExplicit(
                        Date::dateTimeToExcel($value),
                        DataType::TYPE_NUMERIC
                    );

                    $cell->getWorksheet()
                         ->getStyle($cell->getCoordinate())
                         ->getNumberFormat()
                         ->setFormatCode(NumberFormat::FORMAT_DATE_DATETIME);

                    return true;
                }

                return parent::bindValue($cell, $value);
            }
        };

        $export->store('custom-value-binder-export.xlsx');

        $spreadsheet = $this->read(__DIR__ . '/../Data/Disks/Local/custom-value-binder-export.xlsx', 'Xlsx');
        $sheet       = $spreadsheet->getActiveSheet();

        // Check if the cell has the Excel date
        $this->assertSame(Date::dateTimeToExcel(Carbon::now()), $sheet->getCell('A1')->getValue());

        // Check if formatted as datetime
        $this->assertEquals(NumberFormat::FORMAT_DATE_DATETIME, $sheet->getCell('A1')->getStyle()->getNumberFormat()->getFormatCode());

        // Check if the cell has the converted percentage
        $this->assertSame(0.1, $sheet->getCell('B1')->getValue());

        // Check if formatted as percentage
        $this->assertEquals(NumberFormat::FORMAT_PERCENTAGE_00, $sheet->getCell('B1')->getStyle()->getNumberFormat()->getFormatCode());
    }

    /**
     * @test
     */
    public function can_set_a_value_binder_on_import()
    {
        $import = new class extends DefaultValueBinder implements WithCustomValueBinder, ToArray {
            /**
             * {@inheritdoc}
             */
            public function bindValue(Cell $cell, $value)
            {
                if ($cell->getCoordinate() === 'B2') {
                    $cell->setValueExplicit($value, DataType::TYPE_STRING);

                    return true;
                }

                if ($cell->getRow() === 3) {
                    $date = Carbon::instance(Date::excelToDateTimeObject($value));
                    $cell->setValueExplicit($date->toDateTimeString(), DataType::TYPE_STRING);

                    return true;
                }

                return parent::bindValue($cell, $value);
            }

            /**
             * @param array $array
             */
            public function array(array $array)
            {
                Assert::assertSame([
                    [
                        'col1',
                        'col2',
                    ],
                    [
                        1.0, // by default PhpSpreadsheet will convert numbers to float
                        '2', // Forced to be a string
                    ],
                    [
                        '2018-08-06 18:31:46', // Convert Excel datetime to datetime strings
                        '2018-08-07 00:00:00', // Convert Excel date to datetime strings
                    ],
                ], $array);
            }
        };

        $this->app->make(Excel::class)->import($import, 'value-binder-import.xlsx');
    }
}
