<?php

namespace Chubb001\Excel31\Tests\Data\Stubs;

use Chubb001\Excel31\Events\AfterSheet;
use Chubb001\Excel31\Events\AfterImport;
use Chubb001\Excel31\Events\BeforeSheet;
use Chubb001\Excel31\Concerns\Importable;
use Chubb001\Excel31\Concerns\WithEvents;
use Chubb001\Excel31\Events\BeforeImport;

class ImportWithEvents implements WithEvents
{
    use Importable;

    /**
     * @var callable
     */
    public $beforeImport;

    /**
     * @var callable
     */
    public $beforeSheet;

    /**
     * @var callable
     */
    public $afterSheet;

    /**
     * @return array
     */
    public function registerEvents(): array
    {
        return [
            BeforeImport::class => $this->beforeImport ?? function () {
            },
            AfterImport::class => $this->afterImport ?? function () {
            },
            BeforeSheet::class => $this->beforeSheet ?? function () {
            },
            AfterSheet::class => $this->afterSheet ?? function () {
            },
        ];
    }
}
