<?php

namespace Chubb001\Excel31;

use Illuminate\Support\Collection;
use Chubb001\Excel31\Concerns\ToArray;
use Chubb001\Excel31\Concerns\ToModel;
use Chubb001\Excel31\Concerns\ToCollection;
use Chubb001\Excel31\Concerns\WithMappedCells;
use PhpOffice\PhpSpreadsheet\Worksheet\Worksheet;
use Chubb001\Excel31\Concerns\WithCalculatedFormulas;

class MappedReader
{
    /**
     * @param WithMappedCells $import
     * @param Worksheet       $worksheet
     *
     * @throws \PhpOffice\PhpSpreadsheet\Exception
     */
    public function map(WithMappedCells $import, Worksheet $worksheet)
    {
        $mapped = [];
        foreach ($import->mapping() as $name => $coordinate) {
            $cell = Cell::make($worksheet, $coordinate);

            $mapped[$name] = $cell->getValue(
                null,
                $import instanceof WithCalculatedFormulas
            );
        }

        if ($import instanceof ToModel) {
            $model = $import->model($mapped);

            if ($model) {
                $model->saveOrFail();
            }
        }

        if ($import instanceof ToCollection) {
            $import->collection(new Collection($mapped));
        }

        if ($import instanceof ToArray) {
            $import->array($mapped);
        }
    }
}
