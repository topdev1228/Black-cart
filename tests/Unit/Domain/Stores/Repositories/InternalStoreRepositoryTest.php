<?php
declare(strict_types=1);

namespace Tests\Unit\Domain\Stores\Repositories;

use App\Domain\Stores\Models\Store;
use App\Domain\Stores\Models\StoreSetting;
use App\Domain\Stores\Repositories\InternalStoreRepository;
use Illuminate\Support\Facades\Date;
use Tests\TestCase;

class InternalStoreRepositoryTest extends TestCase
{
    public function testItGetsAllStoresUndeleted(): void
    {
        $expectedStores = Store::withoutEvents(function () {
            Store::factory()->count(2)->create([
                'deleted_at' => Date::now(),
            ]);

            return Store::factory()->count(5)->create();
        });
        foreach ($expectedStores as $store) {
            StoreSetting::withoutEvents(function () use ($store) {
                StoreSetting::factory()->for($store)->secure()->create([
                    'name' => 'shopify_oauth_token',
                    'value' => 'token_' . $store->id,
                ]);
            });
        }

        $internalStoreRepository = resolve(InternalStoreRepository::class);
        $actualStores = $internalStoreRepository->getAllUndeleted();

        $this->assertEquals(count($expectedStores), count($actualStores));

        foreach ($expectedStores as $expectedStore) {
            $found = false;
            foreach ($actualStores as $actualStore) {
                if ($expectedStore->id === $actualStore->id) {
                    $found = true;
                    $this->assertEquals('token_' . $expectedStore->id, $actualStore->accessToken);
                    break;
                }
            }
            $this->assertTrue($found);
        }
    }

    public function testItGetsNoStoresAllStoresDeleted(): void
    {
        Store::withoutEvents(function () {
            Store::factory()->count(2)->create([
                'deleted_at' => Date::now(),
            ]);
        });

        $internalStoreRepository = resolve(InternalStoreRepository::class);
        $actualStores = $internalStoreRepository->getAllUndeleted();

        $this->assertEmpty($actualStores);
    }
}
