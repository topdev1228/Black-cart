<?php
declare(strict_types=1);

namespace Feature\Domain\Stores\Http\Controllers;

use App;
use App\Domain\Shopify\Values\JwtPayload;
use App\Domain\Stores\Events\StoreCreated;
use App\Domain\Stores\Models\Store;
use App\Domain\Stores\Values\Store as StoreValue;
use App\Enums\Exceptions\ApiExceptionTypes;
use Config;
use Event;
use Firebase\JWT\JWT;
use Tests\TestCase;

class StoreControllerTest extends TestCase
{
    protected function setUp(): void
    {
        parent::setUp();

        Config::set('services.shopify.client_secret', 'super-secret-key');
    }

    public function testItGetsStores(): void
    {
        $currentStore = Store::withoutEvents(function () {
            Store::factory()->count(5)->create();

            return Store::factory()->create();
        });
        App::context(store: $currentStore);

        $response = $this->getJson('/api/stores', $this->getHeaders($currentStore));

        $response->assertJsonStructure([
            'stores' => [
                '*' => [
                    'name', 'domain', 'email', 'phone', 'owner_name', 'currency', 'primary_locale', 'address1', 'address2',
                    'city', 'state', 'state_code', 'country', 'country_code', 'country_name', 'iana_timezone',
                    'ecommerce_platform', 'ecommerce_platform_store_id', 'ecommerce_platform_plan',
                    'ecommerce_platform_plan_name', 'source',
                ],
            ],
        ]);
        $response->assertJsonMissing(['stores' => ['*' => ['id', 'accessToken']]]);
        $response->assertJsonCount(1, 'stores');
        $response->assertJsonFragment([
            'name' => $currentStore->name,
            'domain' => $currentStore->domain,
            'email' => $currentStore->email,
            'phone' => $currentStore->phone,
            'owner_name' => $currentStore->owner_name,
            'currency' => $currentStore->currency,
            'primary_locale' => $currentStore->primary_locale,
            'address1' => $currentStore->address1,
            'address2' => $currentStore->address2,
            'city' => $currentStore->city,
            'state' => $currentStore->state,
            'state_code' => $currentStore->state_code,
            'country' => $currentStore->country,
            'country_code' => $currentStore->country_code,
            'country_name' => $currentStore->country_name,
            'iana_timezone' => $currentStore->iana_timezone,
            'ecommerce_platform' => $currentStore->ecommerce_platform,
            'ecommerce_platform_store_id' => $currentStore->ecommerce_platform_store_id,
            'ecommerce_platform_plan' => $currentStore->ecommerce_platform_plan,
            'ecommerce_platform_plan_name' => $currentStore->ecommerce_platform_plan_name,
            'source' => $currentStore->source,
        ]);
    }

    public function testItCreatesStore(): void
    {
        Event::fake([
            StoreCreated::class,
        ]);
        $store = StoreValue::builder()->create()->except('settings', 'program')->toArray();
        $response = $this->postJson('/api/stores', $store, $this->getHeaders());

        $response->assertStatus(201);
        $response->assertJsonStructure([
            'store' => [
                'name', 'domain', 'email', 'phone', 'owner_name', 'currency', 'primary_locale', 'address1', 'address2',
                'city', 'state', 'state_code', 'country', 'country_code', 'country_name', 'iana_timezone',
                'ecommerce_platform', 'ecommerce_platform_store_id', 'ecommerce_platform_plan',
                'ecommerce_platform_plan_name', 'source', 'created_at',
            ],
        ]);
        $response->assertJsonFragment(['store' => $store]);
        unset($store['created_at']);
        $this->assertDatabaseHas('stores', $store);
    }

    public function testItCreatesNull(): void
    {
        Event::fake([
            StoreCreated::class,
        ]);

        $store = StoreValue::builder()->null()->create()->toArray();
        $response = $this->postJson('/api/stores', $store, $this->getHeaders());

        $response->assertStatus(201);
        $response->assertJsonStructure([
            'store' => [
                'name', 'domain', 'email', 'phone', 'owner_name', 'currency', 'primary_locale', 'address1', 'address2',
                'city', 'state', 'state_code', 'country', 'country_code', 'country_name', 'iana_timezone',
                'ecommerce_platform', 'ecommerce_platform_store_id', 'ecommerce_platform_plan',
                'ecommerce_platform_plan_name', 'source', 'created_at',
            ],
        ]);
        unset($store['created_at']);
        $this->assertDatabaseHas('stores', $store);

        $data = $response->json();
        unset($data['store']['created_at']);
        unset($store['created_at']);
        $this->assertEquals($data, ['store' => $store]);
    }

    public function testItUpdatesStore(): void
    {
        $currentStore = Store::withoutEvents(function () {
            return Store::factory()->create();
        });
        App::context(store: $currentStore);

        $store = StoreValue::from($currentStore);
        $store->name = 'New Name';

        $data = $store->except('settings', 'program')->toArray();

        $response = $this->putJson('/api/stores', $data, $this->getHeaders($currentStore));

        $response->assertStatus(200);
        $response->assertJsonStructure([
            'store' => [
                'name', 'domain', 'email', 'phone', 'owner_name', 'currency', 'primary_locale', 'address1', 'address2',
                'city', 'state', 'state_code', 'country', 'country_code', 'country_name', 'iana_timezone',
                'ecommerce_platform', 'ecommerce_platform_store_id', 'ecommerce_platform_plan',
                'ecommerce_platform_plan_name', 'source',
            ],
        ]);
        $response->assertJsonMissing(['settings']);
        $response->assertJsonFragment(['store' => $data]);
        unset($data['created_at']);
        $this->assertDatabaseHas('stores', $data);
    }

    public function testItDoesNotUpdatesNonExistentStore(): void
    {
        $currentStore = Store::withoutEvents(function () {
            return Store::factory()->create();
        });

        $store = StoreValue::from($currentStore);
        $store->domain = 'example.com';

        $data = $store->except('settings', 'program')->toArray();

        $response = $this->putJson('/api/stores', $data, $this->getHeaders($currentStore));

        $response->assertStatus(404);
        $response->assertJsonFragment([
            'type' => ApiExceptionTypes::REQUEST_ERROR->value,
            'code' => 'store_not_found',
            'message' => 'Store not found.',
            'errors' => [],
        ]);
    }

    protected function getHeaders(?Store $store = null): array
    {
        return [
            'Authorization' => 'Bearer ' . JWT::encode(
                $store !== null ? (new JwtPayload(
                    domain: $store->domain,
                ))->toArray() : [],
                config('services.shopify.client_secret'),
                'HS256'
            ),
        ];
    }
}
