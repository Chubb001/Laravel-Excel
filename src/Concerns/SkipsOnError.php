<?php

namespace Chubb001\Excel31\Concerns;

use Throwable;

interface SkipsOnError
{
    /**
     * @param Throwable $e
     */
    public function onError(Throwable $e);
}
