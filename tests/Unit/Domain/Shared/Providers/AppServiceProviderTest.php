<?php
declare(strict_types=1);

namespace Tests\Unit\Domain\Shared\Providers;

use App;
use App\Domain\Shared\Exceptions\UndefinedPropertyException;
use App\Domain\Shared\Values\AppContext;
use App\Domain\Stores\Models\Store;
use App\Domain\Stores\Values\Store as StoreValue;
use function spl_object_id;
use Tests\TestCase;

class AppServiceProviderTest extends TestCase
{
    public function testItRegistersAppContext(): void
    {
        $this->assertEquals(spl_object_id(app(AppContext::class)), spl_object_id(app(AppContext::class)));
    }

    public function testExtendsAppContext(): void
    {
        $this->assertEquals(spl_object_id(app()->context()), spl_object_id(App::context()));
    }

    public function testItSetsContextProperties(): void
    {
        $store = Store::withoutEvents(function () {
            return Store::factory()->create();
        });
        $appContext = App::context();
        $appContext->store = StoreValue::from($store);

        $this->assertEquals($store->id, App::context()->store->id);
    }

    public function testItRejectsUnknownProperties(): void
    {
        $this->expectException(UndefinedPropertyException::class);
        $this->expectExceptionMessage('Property test does not exist on App\Domain\Shared\Values\AppContext.');
        App::context()->test = 'test';
    }
}
