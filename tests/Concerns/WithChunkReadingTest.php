<?php

namespace Chubb001\Excel31\Tests\Concerns;

use Exception;
use Throwable;
use Chubb001\Excel31\Reader;
use PHPUnit\Framework\Assert;
use Illuminate\Support\Facades\DB;
use Chubb001\Excel31\Tests\TestCase;
use Illuminate\Database\Eloquent\Model;
use Chubb001\Excel31\Concerns\ToArray;
use Chubb001\Excel31\Concerns\ToModel;
use Chubb001\Excel31\Events\AfterImport;
use Chubb001\Excel31\Concerns\Importable;
use Chubb001\Excel31\Concerns\WithEvents;
use Chubb001\Excel31\Events\BeforeImport;
use Chubb001\Excel31\Events\ImportFailed;
use Chubb001\Excel31\Concerns\WithHeadingRow;
use Chubb001\Excel31\Concerns\WithBatchInserts;
use Chubb001\Excel31\Concerns\WithChunkReading;
use Chubb001\Excel31\Concerns\WithMultipleSheets;
use Chubb001\Excel31\Tests\Data\Stubs\Database\User;
use Chubb001\Excel31\Tests\Data\Stubs\Database\Group;

class WithChunkReadingTest extends TestCase
{
    /**
     * Setup the test environment.
     */
    protected function setUp(): void
    {
        parent::setUp();

        $this->loadLaravelMigrations(['--database' => 'testing']);
        $this->loadMigrationsFrom(dirname(__DIR__) . '/Data/Stubs/Database/Migrations');
    }

    /**
     * @test
     */
    public function can_import_to_model_in_chunks()
    {
        DB::connection()->enableQueryLog();

        $import = new class implements ToModel, WithChunkReading, WithEvents {
            use Importable;

            public $before = false;
            public $after  = false;

            /**
             * @param  array  $row
             *
             * @return Model|null
             */
            public function model(array $row)
            {
                return new User([
                    'name'     => $row[0],
                    'email'    => $row[1],
                    'password' => 'secret',
                ]);
            }

            /**
             * @return int
             */
            public function chunkSize(): int
            {
                return 1;
            }

            /**
             * @return array
             */
            public function registerEvents(): array
            {
                return [
                    BeforeImport::class => function (BeforeImport $event) {
                        Assert::assertInstanceOf(Reader::class, $event->reader);
                        $this->before = true;
                    },
                    AfterImport::class  => function (AfterImport $event) {
                        Assert::assertInstanceOf(Reader::class, $event->reader);
                        $this->after = true;
                    },
                ];
            }
        };

        $import->import('import-users.xlsx');

        $this->assertCount(2, DB::getQueryLog());
        DB::connection()->disableQueryLog();

        $this->assertTrue($import->before, 'BeforeImport was not called.');
        $this->assertTrue($import->after, 'AfterImport was not called.');
    }

    /**
     * @test
     */
    public function can_import_to_model_in_chunks_and_insert_in_batches()
    {
        DB::connection()->enableQueryLog();

        $import = new class implements ToModel, WithChunkReading, WithBatchInserts {
            use Importable;

            /**
             * @param  array  $row
             *
             * @return Model|null
             */
            public function model(array $row)
            {
                return new Group([
                    'name' => $row[0],
                ]);
            }

            /**
             * @return int
             */
            public function chunkSize(): int
            {
                return 1000;
            }

            /**
             * @return int
             */
            public function batchSize(): int
            {
                return 1000;
            }
        };

        $import->import('import-batches.xlsx');

        $this->assertCount(5000 / $import->batchSize(), DB::getQueryLog());
        DB::connection()->disableQueryLog();
    }

    /**
     * @test
     */
    public function can_import_to_model_in_chunks_and_insert_in_batches_with_heading_row()
    {
        DB::connection()->enableQueryLog();

        $import = new class implements ToModel, WithChunkReading, WithBatchInserts, WithHeadingRow {
            use Importable;

            /**
             * @param  array  $row
             *
             * @return Model|null
             */
            public function model(array $row)
            {
                return new Group([
                    'name' => $row['name'],
                ]);
            }

            /**
             * @return int
             */
            public function chunkSize(): int
            {
                return 1000;
            }

            /**
             * @return int
             */
            public function batchSize(): int
            {
                return 1000;
            }
        };

        $import->import('import-batches-with-heading-row.xlsx');

        $this->assertCount(5000 / $import->batchSize(), DB::getQueryLog());
        DB::connection()->disableQueryLog();
    }

    /**
     * @test
     */
    public function can_import_csv_in_chunks_and_insert_in_batches()
    {
        DB::connection()->enableQueryLog();

        $import = new class implements ToModel, WithChunkReading, WithBatchInserts {
            use Importable;

            /**
             * @param  array  $row
             *
             * @return Model|null
             */
            public function model(array $row)
            {
                return new Group([
                    'name' => $row[0],
                ]);
            }

            /**
             * @return int
             */
            public function chunkSize(): int
            {
                return 1000;
            }

            /**
             * @return int
             */
            public function batchSize(): int
            {
                return 1000;
            }
        };

        $import->import('import-batches.csv');

        $this->assertCount(10, DB::getQueryLog());
        DB::connection()->disableQueryLog();
    }

    /**
     * @test
     */
    public function can_import_to_model_in_chunks_and_insert_in_batches_with_multiple_sheets()
    {
        DB::connection()->enableQueryLog();

        $import = new class implements ToModel, WithChunkReading, WithBatchInserts {
            use Importable;

            /**
             * @param  array  $row
             *
             * @return Model|null
             */
            public function model(array $row)
            {
                return new Group([
                    'name' => $row[0],
                ]);
            }

            /**
             * @return int
             */
            public function chunkSize(): int
            {
                return 1000;
            }

            /**
             * @return int
             */
            public function batchSize(): int
            {
                return 1000;
            }
        };

        $import->import('import-batches-multiple-sheets.xlsx');

        $this->assertCount(10, DB::getQueryLog());
        DB::connection()->disableQueryLog();
    }

    /**
     * @test
     */
    public function can_import_to_array_in_chunks()
    {
        $import = new class implements ToArray, WithChunkReading {
            use Importable;

            public $called = 0;

            /**
             * @param  array  $array
             */
            public function array(array $array)
            {
                $this->called++;

                Assert::assertCount(100, $array);
            }

            /**
             * @return int
             */
            public function chunkSize(): int
            {
                return 100;
            }
        };

        $import->import('import-batches.xlsx');

        $this->assertEquals(50, $import->called);
    }

    /**
     * @test
     */
    public function can_import_to_model_in_chunks_and_insert_in_batches_with_multiple_sheets_objects()
    {
        DB::connection()->enableQueryLog();

        $import = new class implements WithMultipleSheets, WithChunkReading {
            use Importable;

            /**
             * @return int
             */
            public function chunkSize(): int
            {
                return 1000;
            }

            /**
             * @return array
             */
            public function sheets(): array
            {
                return [
                    new class implements ToModel, WithBatchInserts {
                        /**
                         * @param  array  $row
                         *
                         * @return Model|null
                         */
                        public function model(array $row)
                        {
                            return new Group([
                                'name' => $row[0],
                            ]);
                        }

                        /**
                         * @return int
                         */
                        public function batchSize(): int
                        {
                            return 1000;
                        }
                    },

                    new class implements ToModel, WithBatchInserts {
                        /**
                         * @param  array  $row
                         *
                         * @return Model|null
                         */
                        public function model(array $row)
                        {
                            return new Group([
                                'name' => $row[0],
                            ]);
                        }

                        /**
                         * @return int
                         */
                        public function batchSize(): int
                        {
                            return 2000;
                        }
                    },
                ];
            }
        };

        $import->import('import-batches-multiple-sheets.xlsx');

        $this->assertCount(10, DB::getQueryLog());
        DB::connection()->disableQueryLog();
    }

    /**
     * @test
     */
    public function can_catch_job_failed_in_chunks()
    {
        $import = new class implements ToModel, WithChunkReading, WithEvents {
            use Importable;

            public $failed = false;

            /**
             * @param  array  $row
             *
             * @return Model|null
             */
            public function model(array $row)
            {
                throw new Exception('Something went wrong in the chunk');
            }

            /**
             * @return int
             */
            public function chunkSize(): int
            {
                return 1;
            }

            /**
             * @return array
             */
            public function registerEvents(): array
            {
                return [
                    ImportFailed::class => function (ImportFailed $event) {
                        Assert::assertInstanceOf(Throwable::class, $event->getException());
                        Assert::assertEquals('Something went wrong in the chunk', $event->e->getMessage());

                        $this->failed = true;
                    },
                ];
            }
        };

        try {
            $import->import('import-users.xlsx');
        } catch (Throwable $e) {
            $this->assertInstanceOf(Exception::class, $e);
            $this->assertEquals('Something went wrong in the chunk', $e->getMessage());
        }

        $this->assertTrue($import->failed, 'ImportFailed event was not called.');
    }
}
