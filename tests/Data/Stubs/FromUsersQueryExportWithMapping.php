<?php

namespace Chubb001\Excel31\Tests\Data\Stubs;

use Illuminate\Database\Query\Builder;
use Chubb001\Excel31\Concerns\FromQuery;
use Chubb001\Excel31\Events\BeforeSheet;
use Chubb001\Excel31\Concerns\Exportable;
use Chubb001\Excel31\Concerns\WithEvents;
use Chubb001\Excel31\Concerns\WithMapping;
use Chubb001\Excel31\Tests\Data\Stubs\Database\User;

class FromUsersQueryExportWithMapping implements FromQuery, WithMapping, WithEvents
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
     * @return array
     */
    public function registerEvents(): array
    {
        return [
            BeforeSheet::class   => function (BeforeSheet $event) {
                $event->sheet->chunkSize(10);
            },
        ];
    }

    /**
     * @param User $row
     *
     * @return array
     */
    public function map($row): array
    {
        return [
            'name' => $row->name,
        ];
    }
}
