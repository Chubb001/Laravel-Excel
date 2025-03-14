<?php

namespace Chubb001\Excel31\Tests\Data\Stubs;

use Chubb001\Excel31\Concerns\Exportable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Chubb001\Excel31\Concerns\WithMultipleSheets;

class ShouldQueueExport implements WithMultipleSheets, ShouldQueue
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
