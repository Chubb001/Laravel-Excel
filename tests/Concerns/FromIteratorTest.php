<?php

namespace Chubb001\Excel31\Tests\Concerns;

use Iterator;
use ArrayIterator;
use Chubb001\Excel31\Tests\TestCase;
use Chubb001\Excel31\Concerns\Exportable;
use Chubb001\Excel31\Concerns\FromIterator;

class FromIteratorTest extends TestCase
{
    /**
     * @test
     */
    public function can_export_from_iterator()
    {
        $export = new class implements FromIterator {
            use Exportable;

            /**
             * @return array
             */
            public function array()
            {
                return [
                    ['test', 'test'],
                    ['test', 'test'],
                ];
            }

            /**
             * @return Iterator
             */
            public function iterator(): Iterator
            {
                return new ArrayIterator($this->array());
            }
        };

        $response = $export->store('from-iterator-store.xlsx');

        $this->assertTrue($response);

        $contents = $this->readAsArray(__DIR__ . '/../Data/Disks/Local/from-iterator-store.xlsx', 'Xlsx');

        $this->assertEquals($export->array(), $contents);
    }
}
