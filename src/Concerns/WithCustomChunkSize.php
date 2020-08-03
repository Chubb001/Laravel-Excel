<?php

namespace Chubb001\Excel31\Concerns;

interface WithCustomChunkSize
{
    /**
     * @return int
     */
    public function chunkSize(): int;
}
