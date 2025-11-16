<?php
declare(strict_types=1);

namespace Tests;

use Carbon\CarbonInterval;
use Exception;
use Feature;
use Illuminate\Foundation\Testing\TestCase as BaseTestCase;
use Illuminate\Foundation\Testing\WithFaker;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Sleep;
use JMac\Testing\Traits\AdditionalAssertions;
use Ramsey\Uuid\Uuid;
use Str;

abstract class TestCase extends BaseTestCase
{
    use AdditionalAssertions;
    use RefreshDatabase;
    use WithFaker;

    protected function setUp(): void
    {
        parent::setUp();

        if (DB::connection()->getDriverName() !== 'sqlite') {
            throw new Exception('Tests are not running with the correct database.');
        }

        $this->setUpFaker();
        $this->freezeTime();
        Feature::fake();
        Sleep::fake();
        Sleep::whenFakingSleep(function (CarbonInterval $duration) {
            // Progress time when faking sleep...
            $this->travel($duration->totalMilliseconds)->milliseconds();
        });

        Http::preventStrayRequests();

        Str::createUuidsUsing(function () {
            return Uuid::uuid4()->toString();
        });
    }

    public function getProtectedAttribute($object, $attribute)
    {
        $attribute = function () use ($attribute) {
            return $this->{$attribute};
        };

        return $attribute->bindTo($object, $object)();
    }

    public function seed($class = 'Database\\Seeders\\DatabaseSeeder')
    {
        (new $class())->run();
    }

    public function loadFixtureData($file, $domain = 'Shared')
    {
        return json_decode(file_get_contents(base_path() . '/tests/Fixtures/Domains/' . $domain . '/Data/' . $file), true);
    }
}
