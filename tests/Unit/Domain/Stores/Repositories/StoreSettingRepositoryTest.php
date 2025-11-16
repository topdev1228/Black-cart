<?php
declare(strict_types=1);

namespace Tests\Unit\Domain\Stores\Repositories;

use App;
use App\Domain\Stores\Enums\StoreStatus;
use App\Domain\Stores\Events\StoreCreated;
use App\Domain\Stores\Events\StoreStatusChangedEvent;
use App\Domain\Stores\Models\Store;
use App\Domain\Stores\Models\StoreSetting;
use App\Domain\Stores\Repositories\StoreSettingRepository;
use App\Domain\Stores\Values\StoreSetting as StoreSettingValue;
use Date;
use Illuminate\Database\Eloquent\Factories\Sequence;
use Illuminate\Support\Facades\Event;
use Tests\TestCase;

class StoreSettingRepositoryTest extends TestCase
{
    protected Store $currentStore;

    protected function setUp(): void
    {
        parent::setUp();

        $this->currentStore = Store::factory()->create();
        App::context(store: $this->currentStore);
    }

    public function testItCreatesStoreSettings(): void
    {
        $storeSettings = StoreSettingValue::builder()
            ->count(5)
            ->state(new Sequence(
                ['name' => 'test', 'value' => 'value'],
                ['name' => 'test2', 'value' => 'value2'],
                ['name' => 'test3', 'value' => 'value3'],
                ['name' => 'test4', 'value' => 'value4'],
                ['name' => 'test5', 'value' => 'value5'],
            ))->create();

        $storeRepository = resolve(StoreSettingRepository::class);
        $storeRepository->save($storeSettings);

        $storeSettings = $this->currentStore->settings()->orderBy('name')->get();
        $this->assertCount(5, $storeSettings);
        $this->assertEquals('value', $storeSettings[0]->value);
        $this->assertEquals('value2', $storeSettings[1]->value);
        $this->assertEquals('value3', $storeSettings[2]->value);
        $this->assertEquals('value4', $storeSettings[3]->value);
        $this->assertEquals('value5', $storeSettings[4]->value);
    }

    public function testItSyncsStoreSettings(): void
    {
        StoreSetting::factory()
            ->count(3)
            ->state(new Sequence(
                ['name' => 'test', 'value' => 'value'],
                ['name' => 'test2', 'value' => 'value2'],
                ['name' => 'test3', 'value' => 'encryptedvalue3', 'is_secure' => true],
            ))
            ->create(['store_id' => $this->currentStore->id]);

        $storeSettings = StoreSettingValue::builder()
            ->count(5)
            ->state(new Sequence(
                ['name' => 'test', 'value' => 'newvalue'],
                ['name' => 'test2', 'value' => 'value2'],
                ['name' => 'test3', 'value' => 'encryptednewvalue3'],
                ['name' => 'test4', 'value' => 'value4'],
                ['name' => 'test5', 'value' => 'encryptedvalue5', 'is_secure' => true],
            ))
            ->create();

        $storeRepository = resolve(StoreSettingRepository::class);
        $storeRepository->save($storeSettings);

        $storeSettings = $this->currentStore->settings()->withSecure()->orderBy('name')->get();
        $this->assertCount(5, $storeSettings);
        $this->assertEquals('newvalue', $storeSettings[0]->value);
        $this->assertEquals('value2', $storeSettings[1]->value);
        $this->assertEquals('encryptednewvalue3', $storeSettings[2]->value);
        $this->assertStringStartsWith('eyJpdiI6', $this->getProtectedAttribute($storeSettings[2], 'attributes')['value']);
        $this->assertEquals('value4', $storeSettings[3]->value);
        $this->assertEquals('encryptedvalue5', $storeSettings[4]->value);
        $this->assertStringStartsWith('eyJpdiI6', $this->getProtectedAttribute($storeSettings[4], 'attributes')['value']);
    }

    public function testItCreatesStoreSettingsWithShopifyOauth(): void
    {
        Event::fake([
            StoreCreated::class,
            StoreStatusChangedEvent::class,
        ]);

        $storeSettings = StoreSettingValue::builder()
            ->count(1)
            ->state(new Sequence(
                ['name' => 'shopify_oauth_token', 'value' => 'value'],
            ))->create();

        $storeRepository = resolve(StoreSettingRepository::class);
        $storeRepository->save($storeSettings);

        $storeSettings = $this->currentStore->settings()->orderBy('name')->get();
        $this->assertCount(2, $storeSettings);
        $this->assertEquals('value', $storeSettings[0]->value);
        $this->assertEquals(StoreStatus::ONBOARDING->value, $storeSettings[1]->value);

        Event::assertDispatched(StoreCreated::class, function (StoreCreated $event) {
            return $event->store->id === $this->currentStore->id;
        });
        Event::assertDispatched(StoreStatusChangedEvent::class, function (StoreStatusChangedEvent $event) {
            return $event->status === StoreStatus::ONBOARDING->value;
        });
    }

    public function testItCreatesStoreSettingsAndDispatchedStoreStatusChangedEvent(): void
    {
        Event::fake([
            StoreStatusChangedEvent::class,
        ]);

        $storeSettings = StoreSettingValue::builder()
            ->count(1)
            ->state(new Sequence(
                ['name' => 'status', 'value' => StoreStatus::ACTIVE->value],
            ))->create();
        $storeRepository = resolve(StoreSettingRepository::class);
        $storeRepository->save($storeSettings);

        $storeSettings = $this->currentStore->settings()->orderBy('name')->get();
        $this->assertCount(1, $storeSettings);
        $this->assertEquals(true, $storeSettings[0]->value);

        Event::assertDispatched(StoreStatusChangedEvent::class, function (StoreStatusChangedEvent $event) {
            return $event->status === StoreStatus::ACTIVE->value;
        });
    }

    public function testItDeletes(): void
    {
        Date::setTestNow('2024-04-09 00:00:00');

        $repository = new StoreSettingRepository();
        $repository->save(StoreSettingValue::builder()->create(['name' => 'test_setting']));
        $repository->delete('test_setting');

        $this->assertDatabaseMissing('store_settings', [
            'name' => 'test_setting',
            'deleted_at' => null,
        ]);

        $this->assertDatabaseHas('store_settings', [
            'name' => 'test_setting',
            'deleted_at' => Date::now()->format('Y-m-d H:i:s'),
        ]);
    }

    public function testItDeleteOnlyForCurrentStore(): void
    {
        Date::setTestNow('2024-04-09 00:00:00');

        $store1 = Store::factory()->create();
        $store2 = Store::factory()->create();

        $repository = new StoreSettingRepository();

        App::context(store: $store1);
        $repository->save(StoreSettingValue::builder()->create(['name' => 'test_setting']));

        App::context(store: $store2);
        $repository->save(StoreSettingValue::builder()->create(['name' => 'test_setting']));

        App::context(store: $store1);
        $repository->delete('test_setting');

        $this->assertDatabaseMissing('store_settings', [
            'name' => 'test_setting',
            'store_id' => $store1->id,
            'deleted_at' => null,
        ]);
        $this->assertDatabaseHas('store_settings', [
            'name' => 'test_setting',
            'store_id' => $store1->id,
            'deleted_at' => Date::now()->format('Y-m-d H:i:s'),
        ]);

        $this->assertDatabaseHas('store_settings', [
            'name' => 'test_setting',
            'store_id' => $store2->id,
            'deleted_at' => null,
        ]);
    }

    public function testItDeletesAll(): void
    {
        Date::setTestNow('2024-04-09 00:00:00');

        $repository = new StoreSettingRepository();
        $repository->save(StoreSettingValue::builder()->create());
        $repository->deleteAll();

        $this->assertDatabaseMissing('store_settings', [
            'store_id' => $this->currentStore->id,
            'deleted_at' => null,
        ]);
        $this->assertDatabaseHas('store_settings', [
            'store_id' => $this->currentStore->id,
            'deleted_at' => Date::now()->format('Y-m-d H:i:s'),
        ]);
    }
}
