<?php

namespace Chubb001\Excel31\Tests\Data\Stubs;

use Chubb001\Excel31\Concerns\Exportable;
use Chubb001\Excel31\Concerns\WithMultipleSheets;

class QueuedExport implements WithMultipleSheets
{
    use Exportable;

    /**
     * @return SheetWith100Rows[]
     */
    public function sheets(): array
    {
        return [
            new SheetWith100Rows('A'),
            new SheetWith100Rows('B'),
            new SheetWith100Rows('C'),
        ];
    }
}
