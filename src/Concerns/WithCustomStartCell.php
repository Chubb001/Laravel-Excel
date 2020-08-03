<?php

namespace Chubb001\Excel31\Concerns;

interface WithCustomStartCell
{
    /**
     * @return string
     */
    public function startCell(): string;
}
