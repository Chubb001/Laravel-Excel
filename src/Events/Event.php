<?php

namespace Chubb001\Excel31\Events;

abstract class Event
{
    /**
     * @return object
     */
    abstract public function getConcernable();

    /**
     * @return mixed
     */
    abstract public function getDelegate();

    /**
     * @param string $concern
     *
     * @return bool
     */
    public function appliesToConcern(string $concern): bool
    {
        return $this->getConcernable() instanceof $concern;
    }
}
