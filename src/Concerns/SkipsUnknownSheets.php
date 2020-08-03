<?php

namespace Chubb001\Excel31\Concerns;

interface SkipsUnknownSheets
{
    /**
     * @param string|int $sheetName
     */
    public function onUnknownSheet($sheetName);
}
