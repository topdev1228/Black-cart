<?php
declare(strict_types=1);

namespace Tests\Unit\Domain\Shared\Traits;

use App;
use App\Domain\Stores\Exceptions\MissingStoreContextException;
use App\Domain\Stores\Models\Store;
use Illuminate\Database\Eloquent\Factories\Sequence;
use Tests\Fixtures\Models\TestStoreId;
use Tests\TestCase;

class CurrentStoreTest extends TestCase
{
    public function testItGetsCurrentStore(): void
    {
        Store::factory()->count(5)->create();
        $currentStore = Store::factory()->create();

        App::context(store: $currentStore);

        $stores = Store::all();
        $this->assertEquals(1, $stores->count());
        $this->assertEquals($currentStore->id, $stores->first()->id);
    }

    public function testItScopesModelToStore(): void
    {
        Store::factory()->count(5)->create();
        $currentStore = Store::factory()->create();

        $storeIds = Store::withoutCurrentStore()->select('id AS store_id')->without('settings')->get();
        TestStoreId::factory()->count(5)->state(new Sequence(...$storeIds->toArray()))->create();
        TestStoreId::factory()->state(['store_id' => $currentStore->id])->create();

        App::context(store: $currentStore);

        $models = TestStoreId::all();
        $this->assertEquals(1, $models->count());
        $this->assertEquals($currentStore->id, $models->first()->store_id);
    }

    public function testItThrowsExceptionWithoutContext(): void
    {
        $store = Store::factory()->create();

        $this->expectException(MissingStoreContextException::class);

        Store::findOrFail($store->id);
    }
}
