<?php

namespace Chubb001\Excel31\Tests\Data\Stubs;

use Illuminate\Database\Eloquent\Model;
use Chubb001\Excel31\Concerns\ToModel;
use Chubb001\Excel31\Concerns\Importable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Chubb001\Excel31\Concerns\WithBatchInserts;
use Chubb001\Excel31\Concerns\WithChunkReading;
use Chubb001\Excel31\Tests\Data\Stubs\Database\Group;

class QueuedImport implements ShouldQueue, ToModel, WithChunkReading, WithBatchInserts
{
    use Importable;

    /**
     * @param array $row
     *
     * @return Model|null
     */
    public function model(array $row)
    {
        return new Group([
            'name' => $row[0],
        ]);
    }

    /**
     * @return int
     */
    public function batchSize(): int
    {
        return 100;
    }

    /**
     * @return int
     */
    public function chunkSize(): int
    {
        return 100;
    }
}
