<?php

namespace Chubb001\Excel31\Concerns;

use Illuminate\Support\Collection;

interface ToCollection
{
    /**
     * @param Collection $collection
     */
    public function collection(Collection $collection);
}
