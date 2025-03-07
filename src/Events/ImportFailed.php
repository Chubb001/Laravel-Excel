<?php

namespace Chubb001\Excel31\Events;

use Throwable;

class ImportFailed
{
    /**
     * @var Throwable
     */
    public $e;

    /**
     * @param  Throwable  $e
     */
    public function __construct(Throwable $e)
    {
        $this->e = $e;
    }

    /**
     * @return Throwable
     */
    public function getException(): Throwable
    {
        return $this->e;
    }
}
