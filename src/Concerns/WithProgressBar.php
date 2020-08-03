<?php

namespace Chubb001\Excel31\Concerns;

use Illuminate\Console\OutputStyle;

interface WithProgressBar
{
    /**
     * @return OutputStyle
     */
    public function getConsoleOutput(): OutputStyle;
}
