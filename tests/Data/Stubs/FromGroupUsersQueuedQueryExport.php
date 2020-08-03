<?php

namespace Chubb001\Excel31\Tests\Data\Stubs;

use Illuminate\Database\Query\Builder;
use Chubb001\Excel31\Concerns\FromQuery;
use Chubb001\Excel31\Concerns\Exportable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Chubb001\Excel31\Concerns\WithMapping;
use Chubb001\Excel31\Concerns\WithCustomChunkSize;
use Chubb001\Excel31\Tests\Data\Stubs\Database\Group;

class FromGroupUsersQueuedQueryExport implements FromQuery, WithCustomChunkSize, ShouldQueue, WithMapping
{
    use Exportable;

    /**
     * @return Builder
     */
    public function query()
    {
        return Group::first()->users();
    }

    /**
     * @param mixed $row
     *
     * @return array
     */
    public function map($row): array
    {
        return [
            $row->name,
            $row->email,
        ];
    }

    /**
     * @return int
     */
    public function chunkSize(): int
    {
        return 10;
    }
}
