<?php

namespace Chubb001\Excel31\Concerns;

interface WithCustomCsvSettings
{
    /**
     * @return array
     */
    public function getCsvSettings(): array;
}
