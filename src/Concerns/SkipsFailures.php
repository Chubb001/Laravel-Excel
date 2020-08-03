<?php

namespace Chubb001\Excel31\Concerns;

use Illuminate\Support\Collection;
use Chubb001\Excel31\Validators\Failure;

trait SkipsFailures
{
    /**
     * @var Failure[]
     */
    protected $failures = [];

    /**
     * @param Failure ...$failures
     */
    public function onFailure(Failure ...$failures)
    {
        $this->failures = array_merge($this->failures, $failures);
    }

    /**
     * @return Failure[]|Collection
     */
    public function failures(): Collection
    {
        return new Collection($this->failures);
    }
}
