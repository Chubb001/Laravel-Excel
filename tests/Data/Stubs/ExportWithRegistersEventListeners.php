<?php

namespace Chubb001\Excel31\Tests\Data\Stubs;

use Chubb001\Excel31\Concerns\Exportable;
use Chubb001\Excel31\Concerns\WithEvents;
use Chubb001\Excel31\Concerns\RegistersEventListeners;

class ExportWithRegistersEventListeners implements WithEvents
{
    use Exportable, RegistersEventListeners;

    /**
     * @var callable
     */
    public static $beforeExport;

    /**
     * @var callable
     */
    public static $beforeWriting;

    /**
     * @var callable
     */
    public static $beforeSheet;

    /**
     * @var callable
     */
    public static $afterSheet;

    public static function beforeExport()
    {
        (static::$beforeExport)(...func_get_args());
    }

    public static function beforeWriting()
    {
        (static::$beforeWriting)(...func_get_args());
    }

    public static function beforeSheet()
    {
        (static::$beforeSheet)(...func_get_args());
    }

    public static function afterSheet()
    {
        (static::$afterSheet)(...func_get_args());
    }
}
