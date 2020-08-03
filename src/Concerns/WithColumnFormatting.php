<?php

namespace Chubb001\Excel31\Concerns;

interface WithColumnFormatting
{
    /**
     * @return array
     */
    public function columnFormats(): array;
}
