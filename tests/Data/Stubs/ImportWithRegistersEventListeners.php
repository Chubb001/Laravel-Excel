<?php

namespace Chubb001\Excel31\Tests\Data\Stubs;

use Chubb001\Excel31\Concerns\Importable;
use Chubb001\Excel31\Concerns\WithEvents;
use Chubb001\Excel31\Concerns\RegistersEventListeners;

class ImportWithRegistersEventListeners implements WithEvents
{
    use Importable, RegistersEventListeners;

    /**
     * @var callable
     */
    public static $beforeImport;

    /**
     * @var callable
     */
    public static $beforeSheet;

    /**
     * @var callable
     */
    public static $afterSheet;

    public static function beforeImport()
    {
        (static::$beforeImport)(...func_get_args());
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
