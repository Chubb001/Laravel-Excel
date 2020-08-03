<?php

namespace Chubb001\Excel31\Concerns;

interface WithBatchInserts
{
    /**
     * @return int
     */
    public function batchSize(): int;
}
