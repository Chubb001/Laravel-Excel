<?php

namespace Chubb001\Excel31\Jobs;

use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;

class QueueImport implements ShouldQueue
{
    use ExtendedQueueable, Dispatchable;

    public function handle()
    {
        //
    }
}
