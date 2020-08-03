<?php

namespace Chubb001\Excel31\Concerns;

use Chubb001\Excel31\Row;

interface OnEachRow
{
    /**
     * @param Row $row
     */
    public function onRow(Row $row);
}
