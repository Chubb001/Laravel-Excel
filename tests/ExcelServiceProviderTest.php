<?php

namespace Chubb001\Excel31\Tests;

use Chubb001\Excel31\Excel;

class ExcelServiceProviderTest extends TestCase
{
    /**
     * @test
     */
    public function is_bound()
    {
        $this->assertTrue($this->app->bound('excel'));
    }

    /**
     * @test
     */
    public function has_aliased()
    {
        $this->assertTrue($this->app->isAlias(Excel::class));
        $this->assertEquals('excel', $this->app->getAlias(Excel::class));
    }
}
