<?php

namespace Chubb001\Excel31\Concerns;

use Illuminate\Contracts\View\View;

interface FromView
{
    /**
     * @return View
     */
    public function view(): View;
}
