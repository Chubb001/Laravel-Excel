<?php

namespace Chubb001\Excel31\Tests\Data\Stubs;

use Illuminate\Database\Query\Builder;
use Chubb001\Excel31\Concerns\FromQuery;
use Chubb001\Excel31\Concerns\Exportable;
use Chubb001\Excel31\Concerns\WithMapping;
use Chubb001\Excel31\Tests\Data\Stubs\Database\User;

class FromUsersQueryExportWithEagerLoad implements FromQuery, WithMapping
{
    use Exportable;

    /**
     * @return Builder
     */
    public function query()
    {
        return User::query()->with([
            'groups' => function ($query) {
                $query->where('name', 'Group 1');
            },
        ])->withCount('groups');
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
            $row->groups_count,
            $row->groups->implode('name', ', '),
        ];
    }
}
