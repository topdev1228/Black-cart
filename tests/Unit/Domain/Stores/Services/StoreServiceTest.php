<?php
declare(strict_types=1);

namespace Tests\Unit\Domain\Stores\Services;

use App;
use App\Domain\Stores\Models\Store;
use App\Domain\Stores\Services\StoreService;
use App\Domain\Stores\Values\Store as StoreValue;
use App\Domain\Stores\Values\StoreSetting;
use Illuminate\Database\Eloquent\ModelNotFoundException;
use InvalidArgumentException;
use Tests\TestCase;

class StoreServiceTest extends TestCase
{
    protected Store $currentStore;

    protected function setUp(): void
    {
        parent::setUp();

        $this->currentStore = Store::withoutEvents(function () {
            return Store::factory()->create();
        });
        App::context(store: $this->currentStore);
    }

    public function testItGetsStoreById(): void
    {
        $storeService = resolve(StoreService::class);

        $store = $storeService->getStore(id: $this->currentStore->id);
        $this->assertEquals($store->id, $this->currentStore->id);
    }

    public function testItGetsStoreUnsafe(): void
    {
        App::context(store: Store::factory()->create());

        $storeService = resolve(StoreService::class);

        $store = $storeService->getStoreUnsafe(id: $this->currentStore->id);
        $this->assertEquals($store->id, $this->currentStore->id);
    }

    public function testItGetsStoreByDomain(): void
    {
        $storeService = resolve(StoreService::class);

        $store = $storeService->getStore(domain: $this->currentStore->domain);
        $this->assertEquals($store->id, $this->currentStore->id);
    }

    public function testItThrowsExceptionOnGetStoreWithoutArgs(): void
    {
        $storeService = resolve(StoreService::class);

        $this->expectException(InvalidArgumentException::class);
        $store = $storeService->getStore();
    }

    public function testItCreatesStore(): void
    {
        $store = StoreValue::builder()->create();

        $storeService = resolve(StoreService::class);
        $newStore = $storeService->create($store);

        $this->assertEquals($store->name, $newStore->name);
        $this->assertEquals($store->domain, $newStore->domain);
        $this->assertEquals($store->email, $newStore->email);
        $this->assertEquals($store->phone, $newStore->phone);
        $this->assertEquals($store->ownerName, $newStore->ownerName);
        $this->assertEquals($store->currency, $newStore->currency);
        $this->assertEquals($store->primaryLocale, $newStore->primaryLocale);
        $this->assertEquals($store->address1, $newStore->address1);
        $this->assertEquals($store->address2, $newStore->address2);
        $this->assertEquals($store->city, $newStore->city);
        $this->assertEquals($store->state, $newStore->state);
        $this->assertEquals($store->stateCode, $newStore->stateCode);
        $this->assertEquals($store->country, $newStore->country);
        $this->assertEquals($store->countryCode, $newStore->countryCode);
        $this->assertEquals($store->countryName, $newStore->countryName);
        $this->assertEquals($store->ianaTimezone, $newStore->ianaTimezone);
        $this->assertEquals($store->ecommercePlatform, $newStore->ecommercePlatform);
        $this->assertEquals($store->ecommercePlatformStoreId, $newStore->ecommercePlatformStoreId);
        $this->assertEquals($store->ecommercePlatformPlan, $newStore->ecommercePlatformPlan);
        $this->assertEquals($store->ecommercePlatformPlanName, $newStore->ecommercePlatformPlanName);
        $this->assertNotEmpty($newStore->id);
    }

    public function testItUpdatesStore(): void
    {
        $store = StoreValue::from($this->currentStore);
        $store->name = 'Test Store';

        $storeService = resolve(StoreService::class);
        $updatedStore = $storeService->update($store);

        $this->assertEquals($store->name, $updatedStore->name);
        $this->assertEquals($store->domain, $updatedStore->domain);
        $this->assertEquals($store->email, $updatedStore->email);
        $this->assertEquals($store->phone, $updatedStore->phone);
        $this->assertEquals($store->ownerName, $updatedStore->ownerName);
        $this->assertEquals($store->currency, $updatedStore->currency);
        $this->assertEquals($store->primaryLocale, $updatedStore->primaryLocale);
        $this->assertEquals($store->address1, $updatedStore->address1);
        $this->assertEquals($store->address2, $updatedStore->address2);
        $this->assertEquals($store->city, $updatedStore->city);
        $this->assertEquals($store->state, $updatedStore->state);
        $this->assertEquals($store->stateCode, $updatedStore->stateCode);
        $this->assertEquals($store->country, $updatedStore->country);
        $this->assertEquals($store->countryCode, $updatedStore->countryCode);
        $this->assertEquals($store->countryName, $updatedStore->countryName);
        $this->assertEquals($store->ianaTimezone, $updatedStore->ianaTimezone);
        $this->assertEquals($store->ecommercePlatform, $updatedStore->ecommercePlatform);
        $this->assertEquals($store->ecommercePlatformStoreId, $updatedStore->ecommercePlatformStoreId);
        $this->assertEquals($store->ecommercePlatformPlan, $updatedStore->ecommercePlatformPlan);
        $this->assertEquals($store->ecommercePlatformPlanName, $updatedStore->ecommercePlatformPlanName);
        $this->assertNotEmpty($updatedStore->id);
    }

    public function testItGetsAllStores(): void
    {
        $storeService = resolve(StoreService::class);
        $stores = $storeService->all();

        $this->assertCount(1, $stores);
        $this->assertEquals($this->currentStore->id, $stores->first()->id);
    }

    public function testItGetsASetting(): void
    {
        $this->currentStore->settings()->create(['name' => 'test', 'value' => 'value']);

        $storeService = resolve(StoreService::class);

        $setting = $storeService->getSetting('test');
        $this->assertEquals($setting->name, 'test');
        $this->assertEquals($setting->value, 'value');
    }

    public function testItDoesNotGetSecureSetting(): void
    {
        $this->currentStore->settings()->create(['name' => 'test', 'value' => 'value', 'is_secure' => true]);

        $storeService = resolve(StoreService::class);

        $this->expectException(ModelNotFoundException::class);
        $storeService->getSetting('test');
    }

    public function testItGetsSettings(): void
    {
        $this->currentStore->settings()->create(['name' => 'test', 'value' => 'value']);
        $this->currentStore->settings()->create(['name' => 'test2', 'value' => 'value2']);
        $this->currentStore->settings()->create(['name' => 'test3', 'value' => 'value3', 'is_secure' => true]);

        $storeService = resolve(StoreService::class);

        $settings = $storeService->getSettings();
        $this->assertCount(2, $settings);
        $this->assertEquals($settings->first()->name, 'test');
        $this->assertEquals($settings->first()->value, 'value');
        $this->assertEquals($settings->last()->name, 'test2');
        $this->assertEquals($settings->last()->value, 'value2');
    }

    public function testItUpdatesSettings(): void
    {
        $this->currentStore->settings()->create(['name' => 'test', 'value' => 'value']);
        $this->currentStore->settings()->create(['name' => 'test2', 'value' => 'value2']);
        $this->currentStore->settings()->create(['name' => 'test3', 'value' => 'value3', 'is_secure' => true]);

        $storeService = resolve(StoreService::class);

        $settings = StoreSetting::collection([
            StoreSetting::from(['name' => 'test2', 'value' => 'newvalue2']),
            StoreSetting::from(['name' => 'test3', 'value' => 'newvalue3']),
            StoreSetting::from(['name' => 'test4', 'value' => 'value4']),
            StoreSetting::from(['name' => 'test5', 'value' => 'encrypted', 'is_secure' => true]),
        ]);

        $newSettings = $storeService->saveSettings($settings);

        $this->assertCount(3, $newSettings);
        $this->assertEquals($newSettings[0]->name, 'test');
        $this->assertEquals($newSettings[0]->value, 'value');
        $this->assertEquals($newSettings[1]->name, 'test2');
        $this->assertEquals($newSettings[1]->value, 'newvalue2');
        $this->assertEquals($newSettings[2]->name, 'test4');
        $this->assertEquals($newSettings[2]->value, 'value4');

        $this->assertDatabaseHas('store_settings', ['name' => 'test3', 'is_secure' => true]);
        $this->assertDatabaseMissing('store_settings', ['name' => 'test3', 'value' => 'newvalue3', 'is_secure' => true]);
        $this->assertDatabaseHas('store_settings', ['name' => 'test5', 'is_secure' => true]);
        $this->assertDatabaseMissing('store_settings', ['name' => 'test5', 'value' => 'encrypted', 'is_secure' => true]);
    }

    public function testItDeletesStore(): void
    {
        $storeService = resolve(StoreService::class);
        $storeService->delete(StoreValue::from($this->currentStore));

        $this->currentStore->refresh();

        $this->assertTrue($this->currentStore->trashed());
    }
}
