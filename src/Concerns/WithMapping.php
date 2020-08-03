<?php

namespace Chubb001\Excel31\Concerns;

interface WithMapping
{
    /**
     * @param mixed $row
     *
     * @return array
     */
    public function map($row): array;
}
