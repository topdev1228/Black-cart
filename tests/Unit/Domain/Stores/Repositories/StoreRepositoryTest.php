<?php
declare(strict_types=1);

namespace Tests\Unit\Domain\Stores\Repositories;

use App;
use App\Domain\Stores\Exceptions\MissingStoreContextException;
use App\Domain\Stores\Models\Store;
use App\Domain\Stores\Repositories\StoreRepository;
use App\Domain\Stores\Values\Store as StoreValue;
use Illuminate\Database\Eloquent\ModelNotFoundException;
use Illuminate\Support\Facades\Date;
use Tests\TestCase;

class StoreRepositoryTest extends TestCase
{
    public function testItGetsStoreById(): void
    {
        $currentStore = Store::withoutEvents(function () {
            Store::factory()->count(5)->create();

            return Store::factory()->create();
        });
        App::context(store: $currentStore);

        $currentStore->settings()->create([
            'name' => 'shopify_oauth_token',
            'value' => 'test',
        ]);

        $storeRepository = resolve(StoreRepository::class);

        $store = $storeRepository->getById($currentStore->id);
        $this->assertEquals($currentStore->domain, $store->domain);
        $this->assertEquals('test', $store->accessToken);
    }

    public function testItDoesNotGetStoreWhenMissingContext(): void
    {
        $currentStore = Store::withoutEvents(function () {
            Store::factory()->count(5)->create();

            return Store::factory()->create();
        });

        $currentStore->settings()->create([
            'name' => 'shopify_oauth_token',
            'value' => 'test',
        ]);

        $this->expectException(MissingStoreContextException::class);

        $storeRepository = resolve(StoreRepository::class);

        $storeRepository->getById($currentStore->id);
    }

    public function testItDoesNotGetStoreWhenMismatchedContext(): void
    {
        App::context(store: Store::factory()->create());

        $currentStore = Store::withoutEvents(function () {
            Store::factory()->count(5)->create();

            return Store::factory()->create();
        });

        $currentStore->settings()->create([
            'name' => 'shopify_oauth_token',
            'value' => 'test',
        ]);

        $this->expectException(ModelNotFoundException::class);

        $storeRepository = resolve(StoreRepository::class);

        $storeRepository->getById($currentStore->id);
    }

    public function testItGetsStoreByDomain(): void
    {
        $currentStore = Store::withoutEvents(function () {
            Store::factory()->count(5)->create();

            return Store::factory()->create();
        });
        App::context(store: $currentStore);

        $currentStore->settings()->create([
            'name' => 'shopify_oauth_token',
            'value' => 'test',
        ]);

        $storeRepository = resolve(StoreRepository::class);

        $store = $storeRepository->getByDomain($currentStore->domain);
        $this->assertEquals($currentStore->id, $store->id);
        $this->assertEquals('test', $store->accessToken);
    }

    public function testItCreatesANewStore(): void
    {
        $storeValue = StoreValue::builder()->create();

        $storeRepository = resolve(StoreRepository::class);
        $store = $storeRepository->create($storeValue);

        $this->assertEquals($storeValue->name, $store->name);
        $this->assertEquals($storeValue->domain, $store->domain);
        $this->assertEquals($storeValue->email, $store->email);
        $this->assertEquals($storeValue->phone, $store->phone);
        $this->assertEquals($storeValue->ownerName, $store->ownerName);
        $this->assertEquals($storeValue->currency, $store->currency);
        $this->assertEquals($storeValue->primaryLocale, $store->primaryLocale);
        $this->assertEquals($storeValue->address1, $store->address1);
        $this->assertEquals($storeValue->address2, $store->address2);
        $this->assertEquals($storeValue->city, $store->city);
        $this->assertEquals($storeValue->state, $store->state);
        $this->assertEquals($storeValue->stateCode, $store->stateCode);
        $this->assertEquals($storeValue->country, $store->country);
        $this->assertEquals($storeValue->countryCode, $store->countryCode);
        $this->assertEquals($storeValue->countryName, $store->countryName);
        $this->assertEquals($storeValue->ianaTimezone, $store->ianaTimezone);
        $this->assertEquals($storeValue->ecommercePlatform, $store->ecommercePlatform);
        $this->assertEquals($storeValue->ecommercePlatformStoreId, $store->ecommercePlatformStoreId);
        $this->assertEquals($storeValue->ecommercePlatformPlan, $store->ecommercePlatformPlan);
        $this->assertEquals($storeValue->ecommercePlatformPlanName, $store->ecommercePlatformPlanName);
    }

    public function testItUpdatesStore(): void
    {
        $currentStore = Store::withoutEvents(function () {
            return Store::factory()->create();
        });
        App::context(store: $currentStore);

        $storeValue = StoreValue::from($currentStore);
        $storeValue->name = 'New Name';

        $storeRepository = resolve(StoreRepository::class);
        $store = $storeRepository->update($storeValue);

        $this->assertEquals('New Name', $store->name);
        $this->assertEquals($storeValue->domain, $store->domain);
        $this->assertEquals($storeValue->email, $store->email);
        $this->assertEquals($storeValue->phone, $store->phone);
        $this->assertEquals($storeValue->ownerName, $store->ownerName);
        $this->assertEquals($storeValue->currency, $store->currency);
        $this->assertEquals($storeValue->primaryLocale, $store->primaryLocale);
        $this->assertEquals($storeValue->address1, $store->address1);
        $this->assertEquals($storeValue->address2, $store->address2);
        $this->assertEquals($storeValue->city, $store->city);
        $this->assertEquals($storeValue->state, $store->state);
        $this->assertEquals($storeValue->stateCode, $store->stateCode);
        $this->assertEquals($storeValue->country, $store->country);
        $this->assertEquals($storeValue->countryCode, $store->countryCode);
        $this->assertEquals($storeValue->countryName, $store->countryName);
        $this->assertEquals($storeValue->ianaTimezone, $store->ianaTimezone);
        $this->assertEquals($storeValue->ecommercePlatform, $store->ecommercePlatform);
        $this->assertEquals($storeValue->ecommercePlatformStoreId, $store->ecommercePlatformStoreId);
        $this->assertEquals($storeValue->ecommercePlatformPlan, $store->ecommercePlatformPlan);
        $this->assertEquals($storeValue->ecommercePlatformPlanName, $store->ecommercePlatformPlanName);
    }

    public function testItGetsAllStoresWithScope(): void
    {
        $currentStore = Store::withoutEvents(function () {
            Store::factory()->count(5)->create();

            return Store::factory()->create();
        });
        App::context(store: $currentStore);

        $storeRepository = resolve(StoreRepository::class);

        $this->assertEquals(1, $storeRepository->all()->count());
    }

    public function testGetByIdThrowsExceptionsWithoutScope(): void
    {
        $storeRepository = resolve(StoreRepository::class);

        $this->expectException(MissingStoreContextException::class);
        $storeRepository->getById('test');
    }

    public function testGetAllThrowsExceptionsWithoutScope(): void
    {
        $storeRepository = resolve(StoreRepository::class);

        $this->expectException(MissingStoreContextException::class);
        $storeRepository->all();
    }

    public function testItDeletesStores(): void
    {
        $currentStore = Store::withoutEvents(function () {
            Store::factory()->count(5)->create();

            return Store::factory()->create();
        });
        App::context(store: $currentStore);

        $storeValue = StoreValue::from($currentStore);

        $storeRepository = resolve(StoreRepository::class);
        $storeRepository->delete($storeValue);

        $this->assertDatabaseHas('stores', ['id' => $currentStore->id, 'deleted_at' => Date::now()]);
    }
}
