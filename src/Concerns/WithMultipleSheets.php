<?php

namespace Chubb001\Excel31\Concerns;

interface WithMultipleSheets
{
    /**
     * @return array
     */
    public function sheets(): array;
}
