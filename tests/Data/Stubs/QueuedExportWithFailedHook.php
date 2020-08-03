<?php

namespace Chubb001\Excel31\Tests\Data\Stubs;

use Exception;
use PHPUnit\Framework\Assert;
use Chubb001\Excel31\Concerns\Exportable;
use Chubb001\Excel31\Concerns\WithMapping;
use Illuminate\Database\Eloquent\Collection;
use Chubb001\Excel31\Concerns\FromCollection;
use Chubb001\Excel31\Tests\Data\Stubs\Database\User;

class QueuedExportWithFailedHook implements FromCollection, WithMapping
{
    use Exportable;

    /**
     * @var bool
     */
    public $failed = false;

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
        throw new Exception('we expect this');
    }

    /**
     * @param Exception $exception
     */
    public function failed(Exception $exception)
    {
        Assert::assertEquals('we expect this', $exception->getMessage());

        app()->bind('queue-has-failed', function () {
            return true;
        });
    }
}
