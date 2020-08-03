<?php

namespace Chubb001\Excel31\Concerns;

use Illuminate\Database\Query\Builder;

interface FromQuery
{
    /**
     * @return Builder
     */
    public function query();
}
