<?php

namespace Chubb001\Excel31\Tests\Data\Stubs;

use Illuminate\Database\Query\Builder;
use Chubb001\Excel31\Concerns\FromQuery;
use Chubb001\Excel31\Concerns\Exportable;
use Chubb001\Excel31\Concerns\WithCustomChunkSize;
use Chubb001\Excel31\Tests\Data\Stubs\Database\User;

class FromUsersQueryExport implements FromQuery, WithCustomChunkSize
{
    use Exportable;

    /**
     * @return Builder
     */
    public function query()
    {
        return User::query();
    }

    /**
     * @return int
     */
    public function chunkSize(): int
    {
        return 10;
    }
}
