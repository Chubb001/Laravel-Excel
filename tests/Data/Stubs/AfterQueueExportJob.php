<?php

namespace Chubb001\Excel31\Tests\Data\Stubs;

use Illuminate\Bus\Queueable;
use Chubb001\Excel31\Tests\TestCase;
use Illuminate\Contracts\Queue\ShouldQueue;

class AfterQueueExportJob implements ShouldQueue
{
    use Queueable;

    /**
     * @var string
     */
    private $filePath;

    /**
     * @param string $filePath
     */
    public function __construct(string $filePath)
    {
        $this->filePath = $filePath;
    }

    public function handle()
    {
        TestCase::assertFileExists($this->filePath);
    }
}
