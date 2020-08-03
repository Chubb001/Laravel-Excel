<?php

namespace Chubb001\Excel31\Concerns;

interface WithChunkReading
{
    /**
     * @return int
     */
    public function chunkSize(): int;
}
