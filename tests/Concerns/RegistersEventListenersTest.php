<?php

namespace Chubb001\Excel31\Tests\Concerns;

use Chubb001\Excel31\Sheet;
use Chubb001\Excel31\Reader;
use Chubb001\Excel31\Writer;
use Chubb001\Excel31\Tests\TestCase;
use Chubb001\Excel31\Events\AfterSheet;
use Chubb001\Excel31\Events\BeforeSheet;
use Chubb001\Excel31\Events\BeforeExport;
use Chubb001\Excel31\Events\BeforeImport;
use Chubb001\Excel31\Events\BeforeWriting;
use Chubb001\Excel31\Tests\Data\Stubs\ExportWithEvents;
use Symfony\Component\HttpFoundation\BinaryFileResponse;
use Chubb001\Excel31\Tests\Data\Stubs\BeforeExportListener;
use Chubb001\Excel31\Tests\Data\Stubs\ExportWithRegistersEventListeners;
use Chubb001\Excel31\Tests\Data\Stubs\ImportWithRegistersEventListeners;

class RegistersEventListenersTest extends TestCase
{
    /**
     * @test
     */
    public function events_get_called_when_exporting()
    {
        $event = new ExportWithRegistersEventListeners();

        $eventsTriggered = 0;

        $event::$beforeExport = function ($event) use (&$eventsTriggered) {
            $this->assertInstanceOf(BeforeExport::class, $event);
            $this->assertInstanceOf(Writer::class, $event->writer);
            $eventsTriggered++;
        };

        $event::$beforeWriting = function ($event) use (&$eventsTriggered) {
            $this->assertInstanceOf(BeforeWriting::class, $event);
            $this->assertInstanceOf(Writer::class, $event->writer);
            $eventsTriggered++;
        };

        $event::$beforeSheet = function ($event) use (&$eventsTriggered) {
            $this->assertInstanceOf(BeforeSheet::class, $event);
            $this->assertInstanceOf(Sheet::class, $event->sheet);
            $eventsTriggered++;
        };

        $event::$afterSheet = function ($event) use (&$eventsTriggered) {
            $this->assertInstanceOf(AfterSheet::class, $event);
            $this->assertInstanceOf(Sheet::class, $event->sheet);
            $eventsTriggered++;
        };

        $this->assertInstanceOf(BinaryFileResponse::class, $event->download('filename.xlsx'));
        $this->assertEquals(4, $eventsTriggered);
    }

    /**
     * @test
     */
    public function events_get_called_when_importing()
    {
        $event = new ImportWithRegistersEventListeners();

        $eventsTriggered = 0;

        $event::$beforeImport = function ($event) use (&$eventsTriggered) {
            $this->assertInstanceOf(BeforeImport::class, $event);
            $this->assertInstanceOf(Reader::class, $event->reader);
            $eventsTriggered++;
        };

        $event::$beforeSheet = function ($event) use (&$eventsTriggered) {
            $this->assertInstanceOf(BeforeSheet::class, $event);
            $this->assertInstanceOf(Sheet::class, $event->sheet);
            $eventsTriggered++;
        };

        $event::$afterSheet = function ($event) use (&$eventsTriggered) {
            $this->assertInstanceOf(AfterSheet::class, $event);
            $this->assertInstanceOf(Sheet::class, $event->sheet);
            $eventsTriggered++;
        };

        $event->import('import.xlsx');
        $this->assertEquals(3, $eventsTriggered);
    }

    /**
     * @test
     */
    public function can_have_invokable_class_as_listener()
    {
        $event = new ExportWithEvents();

        $event->beforeExport = new BeforeExportListener(function ($event) {
            $this->assertInstanceOf(BeforeExport::class, $event);
            $this->assertInstanceOf(Writer::class, $event->writer);
        });

        $this->assertInstanceOf(BinaryFileResponse::class, $event->download('filename.xlsx'));
    }
}
