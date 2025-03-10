<?php

namespace Chubb001\Excel31\Mixins;

use Illuminate\Support\Collection;
use Chubb001\Excel31\Concerns\Exportable;
use Chubb001\Excel31\Concerns\WithHeadings;
use Chubb001\Excel31\Concerns\FromCollection;

class StoreCollection
{
    /**
     * @return callable
     */
    public function storeExcel()
    {
        return function (string $filePath, string $disk = null, string $writerType = null, $withHeadings = false) {
            $export = new class($this, $withHeadings) implements FromCollection, WithHeadings {
                use Exportable;

                /**
                 * @var bool
                 */
                private $withHeadings;

                /**
                 * @var Collection
                 */
                private $collection;

                /**
                 * @param Collection $collection
                 * @param bool       $withHeadings
                 */
                public function __construct(Collection $collection, bool $withHeadings = false)
                {
                    $this->collection   = $collection->toBase();
                    $this->withHeadings = $withHeadings;
                }

                /**
                 * @return Collection
                 */
                public function collection()
                {
                    return $this->collection;
                }

                /**
                 * @return array
                 */
                public function headings(): array
                {
                    if (!$this->withHeadings) {
                        return [];
                    }

                    return is_array($first = $this->collection->first())
                        ? $this->collection->collapse()->keys()->all()
                        : array_keys($first->toArray());
                }
            };

            return $export->store($filePath, $disk, $writerType);
        };
    }
}
