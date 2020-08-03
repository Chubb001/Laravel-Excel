<?php

namespace Chubb001\Excel31\Tests\Data\Stubs;

use Chubb001\Excel31\Concerns\Exportable;
use Chubb001\Excel31\Concerns\WithMapping;
use Illuminate\Database\Eloquent\Collection;
use Chubb001\Excel31\Concerns\FromCollection;
use Chubb001\Excel31\Tests\Data\Stubs\Database\User;

class EloquentCollectionWithMappingExport implements FromCollection, WithMapping
{
    use Exportable;

    /**
     * @return Collection
     */
    public function collection()
    {
        return collect([
            new User([
                'firstname' => 'Patrick',
                'lastname'  => 'Brouwers',
            ]),
        ]);
    }

    /**
     * @param User $user
     *
     * @return array
     */
    public function map($user): array
    {
        return [
            $user->firstname,
            $user->lastname,
        ];
    }
}
