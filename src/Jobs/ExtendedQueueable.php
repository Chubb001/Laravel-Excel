<?php

namespace Chubb001\Excel31\Jobs;

use Illuminate\Bus\Queueable;

trait ExtendedQueueable
{
    use Queueable {
        chain as originalChain;
    }

    /**
     * @param $chain
     *
     * @return $this
     */
    public function chain($chain)
    {
        collect($chain)->each(function ($job) {
            $this->chained[] = serialize($job);
        });

        return $this;
    }
}
