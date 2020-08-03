<?php

namespace Chubb001\Excel31\Tests\Data\Stubs;

use Illuminate\Support\Facades\DB;
use Illuminate\Database\Query\Builder;
use Chubb001\Excel31\Concerns\FromQuery;
use Chubb001\Excel31\Concerns\Exportable;
use Chubb001\Excel31\Concerns\WithCustomChunkSize;

class FromNonEloquentQueryExport implements FromQuery, WithCustomChunkSize
{
    use Exportable;

    /**
     * @return Builder
     */
    public function query()
    {
        return DB::table('users')->select('name')->orderBy('id');
    }

    /**
     * @return int
     */
    public function chunkSize(): int
    {
        return 10;
    }
}
