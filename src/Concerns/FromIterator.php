<?php

namespace Chubb001\Excel31\Concerns;

use Iterator;

interface FromIterator
{
    /**
     * @return Iterator
     */
    public function iterator(): Iterator;
}
