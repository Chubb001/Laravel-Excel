<?php

namespace Chubb001\Excel31\Tests\Data\Stubs;

use Chubb001\Excel31\Concerns\WithTitle;
use Chubb001\Excel31\Concerns\Exportable;

class WithTitleExport implements WithTitle
{
    use Exportable;

    /**
     * @return string
     */
    public function title(): string
    {
        return 'given-title';
    }
}
