<?php

namespace Chubb001\Excel31\Imports;

use Chubb001\Excel31\Row;
use Chubb001\Excel31\Concerns\WithStartRow;
use Chubb001\Excel31\Concerns\WithHeadingRow;
use PhpOffice\PhpSpreadsheet\Worksheet\Worksheet;

class HeadingRowExtractor
{
    /**
     * @const int
     */
    const DEFAULT_HEADING_ROW = 1;

    /**
     * @param WithHeadingRow|mixed $importable
     *
     * @return int
     */
    public static function headingRow($importable): int
    {
        return method_exists($importable, 'headingRow')
            ? $importable->headingRow()
            : self::DEFAULT_HEADING_ROW;
    }

    /**
     * @param WithHeadingRow|mixed $importable
     *
     * @return int
     */
    public static function determineStartRow($importable): int
    {
        if ($importable instanceof WithStartRow) {
            return $importable->startRow();
        }

        // The start row is the row after the heading row if we have one!
        return $importable instanceof WithHeadingRow
            ? self::headingRow($importable) + 1
            : self::DEFAULT_HEADING_ROW;
    }

    /**
     * @param Worksheet            $worksheet
     * @param WithHeadingRow|mixed $importable
     *
     * @return array
     */
    public static function extract(Worksheet $worksheet, $importable): array
    {
        if (!$importable instanceof WithHeadingRow) {
            return [];
        }

        $headingRowNumber = self::headingRow($importable);
        $rows             = iterator_to_array($worksheet->getRowIterator($headingRowNumber, $headingRowNumber));
        $headingRow       = head($rows);

        return HeadingRowFormatter::format((new Row($headingRow))->toArray(null, false, false));
    }
}
