<?php

namespace Chubb001\Excel31\Tests\Data\Stubs;

use Chubb001\Excel31\Writer;
use Illuminate\Support\Collection;
use Chubb001\Excel31\Tests\TestCase;
use Chubb001\Excel31\Concerns\WithTitle;
use Chubb001\Excel31\Concerns\Exportable;
use Chubb001\Excel31\Concerns\WithEvents;
use Chubb001\Excel31\Events\BeforeWriting;
use Chubb001\Excel31\Concerns\FromCollection;
use Chubb001\Excel31\Concerns\ShouldAutoSize;
use Chubb001\Excel31\Concerns\RegistersEventListeners;

class SheetWith100Rows implements FromCollection, WithTitle, ShouldAutoSize, WithEvents
{
    use Exportable, RegistersEventListeners;

    /**
     * @var string
     */
    private $title;

    /**
     * @param string $title
     */
    public function __construct(string $title)
    {
        $this->title = $title;
    }

    /**
     * @return Collection
     */
    public function collection()
    {
        $collection = new Collection;
        for ($i = 0; $i < 100; $i++) {
            $row = new Collection();
            for ($j = 0; $j < 5; $j++) {
                $row[] = $this->title() . '-' . $i . '-' . $j;
            }

            $collection->push($row);
        }

        return $collection;
    }

    /**
     * @return string
     */
    public function title(): string
    {
        return $this->title;
    }

    /**
     * @param BeforeWriting $event
     */
    public static function beforeWriting(BeforeWriting $event)
    {
        TestCase::assertInstanceOf(Writer::class, $event->writer);
    }
}
