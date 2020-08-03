<?php

namespace Chubb001\Excel31\Tests\Data\Stubs;

use Chubb001\Excel31\Events\AfterSheet;
use Chubb001\Excel31\Events\BeforeSheet;
use Chubb001\Excel31\Concerns\Exportable;
use Chubb001\Excel31\Concerns\WithEvents;
use Chubb001\Excel31\Events\BeforeExport;
use Chubb001\Excel31\Events\BeforeWriting;

class ExportWithEvents implements WithEvents
{
    use Exportable;

    /**
     * @var callable
     */
    public $beforeExport;

    /**
     * @var callable
     */
    public $beforeWriting;

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
            BeforeExport::class  => $this->beforeExport ?? function () {
            },
            BeforeWriting::class => $this->beforeWriting ?? function () {
            },
            BeforeSheet::class   => $this->beforeSheet ?? function () {
            },
            AfterSheet::class    => $this->afterSheet ?? function () {
            },
        ];
    }
}
