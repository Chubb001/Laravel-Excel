<?php

namespace Chubb001\Excel31\Transactions;

interface TransactionHandler
{
    /**
     * @param callable $callback
     *
     * @return mixed
     */
    public function __invoke(callable $callback);
}
