<?php

namespace Chubb001\Excel31\Transactions;

class NullTransactionHandler implements TransactionHandler
{
    /**
     * @param callable $callback
     *
     * @return mixed
     */
    public function __invoke(callable $callback)
    {
        return $callback();
    }
}
