<?php

namespace Chubb001\Excel31\Tests\Concerns;

use Illuminate\Support\Str;
use PHPUnit\Framework\Assert;
use Chubb001\Excel31\Tests\TestCase;
use Chubb001\Excel31\Concerns\ToArray;
use Chubb001\Excel31\Concerns\ToModel;
use Chubb001\Excel31\Concerns\Importable;
use Chubb001\Excel31\Concerns\WithMappedCells;
use Chubb001\Excel31\Tests\Data\Stubs\Database\User;

class WithMappedCellsTest extends TestCase
{
    /**
     * Setup the test environment.
     */
    protected function setUp(): void
    {
        parent::setUp();

        $this->loadLaravelMigrations(['--database' => 'testing']);
    }

    /**
     * @test
     */
    public function can_import_with_references_to_cells()
    {
        $import = new class implements WithMappedCells, ToArray {
            use Importable;

            /**
             * @return array
             */
            public function mapping(): array
            {
                return [
                    'name'  => 'B1',
                    'email' => 'B2',
                ];
            }

            /**
             * @param array $array
             */
            public function array(array $array)
            {
                Assert::assertEquals([
                    'name'  => 'Patrick Brouwers',
                    'email' => 'patrick@maatwebsite.nl',
                ], $array);
            }
        };

        $import->import('mapped-import.xlsx');
    }

    /**
     * @test
     */
    public function can_import_with_references_to_cells_to_model()
    {
        $import = new class implements WithMappedCells, ToModel {
            use Importable;

            /**
             * @return array
             */
            public function mapping(): array
            {
                return [
                    'name'  => 'B1',
                    'email' => 'B2',
                ];
            }

            /**
             * @param array $array
             *
             * @return User
             */
            public function model(array $array)
            {
                Assert::assertEquals([
                    'name'  => 'Patrick Brouwers',
                    'email' => 'patrick@maatwebsite.nl',
                ], $array);

                $array['password'] = Str::random();

                return new User($array);
            }
        };

        $import->import('mapped-import.xlsx');

        $this->assertDatabaseHas('users', [
            'name'  => 'Patrick Brouwers',
            'email' => 'patrick@maatwebsite.nl',
        ]);
    }
}
