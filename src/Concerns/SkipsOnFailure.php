<?php

namespace Chubb001\Excel31\Concerns;

use Chubb001\Excel31\Validators\Failure;

interface SkipsOnFailure
{
    /**
     * @param Failure[] $failures
     */
    public function onFailure(Failure ...$failures);
}
