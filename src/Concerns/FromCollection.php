<?php

namespace Chubb001\Excel31\Concerns;

use Illuminate\Support\Collection;

interface FromCollection
{
    /**
     * @return Collection
     */
    public function collection();
}
