<?php

namespace Chubb001\Excel31\Tests\Concerns;

use PHPUnit\Framework\Assert;
use Illuminate\Validation\Rule;
use Chubb001\Excel31\Tests\TestCase;
use Illuminate\Database\Eloquent\Model;
use Chubb001\Excel31\Concerns\ToModel;
use Chubb001\Excel31\Validators\Failure;
use Chubb001\Excel31\Concerns\Importable;
use Chubb001\Excel31\Concerns\SkipsFailures;
use Chubb001\Excel31\Concerns\SkipsOnFailure;
use Chubb001\Excel31\Concerns\WithValidation;
use Chubb001\Excel31\Concerns\WithBatchInserts;
use Chubb001\Excel31\Tests\Data\Stubs\Database\User;

class SkipsOnFailureTest extends TestCase
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
    public function can_skip_on_error()
    {
        $import = new class implements ToModel, WithValidation, SkipsOnFailure {
            use Importable;

            public $failures = 0;

            /**
             * @param array $row
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
             * @return array
             */
            public function rules(): array
            {
                return [
                    '1' => Rule::in(['patrick@maatwebsite.nl']),
                ];
            }

            /**
             * @param Failure[] $failures
             */
            public function onFailure(Failure ...$failures)
            {
                $failure = $failures[0];

                Assert::assertEquals(2, $failure->row());
                Assert::assertEquals('1', $failure->attribute());
                Assert::assertEquals(['The selected 1 is invalid.'], $failure->errors());
                Assert::assertEquals(['Taylor Otwell', 'taylor@laravel.com'], $failure->values());

                $this->failures += \count($failures);
            }
        };

        $import->import('import-users.xlsx');

        $this->assertEquals(1, $import->failures);

        // Shouldn't have rollbacked other imported rows.
        $this->assertDatabaseHas('users', [
            'email' => 'patrick@maatwebsite.nl',
        ]);

        // Should have skipped inserting
        $this->assertDatabaseMissing('users', [
            'email' => 'taylor@laravel.com',
        ]);
    }

    /**
     * @test
     */
    public function skips_only_failed_rows_in_batch()
    {
        $import = new class implements ToModel, WithValidation, WithBatchInserts, SkipsOnFailure {
            use Importable;

            public $failures = 0;

            /**
             * @param array $row
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
             * @return array
             */
            public function rules(): array
            {
                return [
                    '1' => Rule::in(['patrick@maatwebsite.nl']),
                ];
            }

            /**
             * @param Failure[] $failures
             */
            public function onFailure(Failure ...$failures)
            {
                $failure = $failures[0];

                Assert::assertEquals(2, $failure->row());
                Assert::assertEquals('1', $failure->attribute());
                Assert::assertEquals(['The selected 1 is invalid.'], $failure->errors());

                $this->failures += \count($failures);
            }

            /**
             * @return int
             */
            public function batchSize(): int
            {
                return 100;
            }
        };

        $import->import('import-users.xlsx');

        $this->assertEquals(1, $import->failures);

        // Shouldn't have rollbacked/skipped the rest of the batch.
        $this->assertDatabaseHas('users', [
            'email' => 'patrick@maatwebsite.nl',
        ]);

        // Should have skipped inserting
        $this->assertDatabaseMissing('users', [
            'email' => 'taylor@laravel.com',
        ]);
    }

    /**
     * @test
     */
    public function can_skip_failures_and_collect_all_failures_at_the_end()
    {
        $import = new class implements ToModel, WithValidation, SkipsOnFailure {
            use Importable, SkipsFailures;

            /**
             * @param array $row
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
             * @return array
             */
            public function rules(): array
            {
                return [
                    '1' => Rule::in(['patrick@maatwebsite.nl']),
                ];
            }
        };

        $import->import('import-users.xlsx');

        $this->assertCount(1, $import->failures());

        /** @var Failure $failure */
        $failure = $import->failures()->first();

        $this->assertEquals(2, $failure->row());
        $this->assertEquals('1', $failure->attribute());
        $this->assertEquals(['The selected 1 is invalid.'], $failure->errors());

        // Shouldn't have rollbacked other imported rows.
        $this->assertDatabaseHas('users', [
            'email' => 'patrick@maatwebsite.nl',
        ]);

        // Should have skipped inserting
        $this->assertDatabaseMissing('users', [
            'email' => 'taylor@laravel.com',
        ]);
    }
}
