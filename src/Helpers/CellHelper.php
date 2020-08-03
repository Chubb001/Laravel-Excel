<?php

namespace Chubb001\Excel31\Helpers;

class CellHelper
{
    /**
     * @param string $coordinate
     *
     * @return string
     */
    public static function getColumnFromCoordinate(string $coordinate): string
    {
        return preg_replace('/[0-9]/', '', $coordinate);
    }
}
