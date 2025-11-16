<?php
declare(strict_types=1);

namespace Feature\Domain\Stores\Http\Controllers;

use App;
use App\Domain\Shopify\Values\JwtPayload;
use App\Domain\Stores\Models\Store;
use App\Domain\Stores\Models\StoreSetting;
use App\Domain\Stores\Values\StoreSetting as StoreSettingValue;
use App\Domain\Stores\Values\StoreSettings;
use Config;
use Firebase\JWT\JWT;
use Illuminate\Database\Eloquent\Factories\Sequence;
use Tests\TestCase;

class StoreSettingControllerTest extends TestCase
{
    protected Store $currentStore;
    protected array $headers;

    protected function setUp(): void
    {
        parent::setUp();

        $this->currentStore = Store::withoutEvents(function () {
            return Store::factory()->create();
        });
        App::context(store: $this->currentStore);

        Config::set('services.shopify.client_secret', 'super-secret-key');

        $this->headers = [
            'Authorization' => 'Bearer ' . JWT::encode(
                (new JwtPayload(
                    domain: $this->currentStore->domain,
                ))->toArray(),
                config('services.shopify.client_secret'),
                'HS256'
            ),
        ];
    }

    public function testItGetsSettings(): void
    {
        StoreSetting::factory()
            ->count(3)
            ->state(new Sequence(
                ['name' => 'test', 'value' => 'value'],
                ['name' => 'test2', 'value' => 'value2'],
                ['name' => 'test3', 'value' => 'encrypted', 'is_secure' => true],
            ))
            ->for($this->currentStore)
            ->create();

        $response = $this->getJson('/api/stores/settings', $this->headers);
        $response->assertStatus(200);
        $response->assertJsonCount(2, 'settings');
        $response->assertJsonStructure(['settings' => ['test' => ['name', 'value'], 'test2' => ['name', 'value']]]);
        $response->assertJsonFragment([
            'settings' => [
                'test' => ['name' => 'test', 'value' => 'value'],
                'test2' => ['name' => 'test2', 'value' => 'value2'],
            ],
        ]);
    }

    public function testItUpdatesStoreSettings(): void
    {
        StoreSetting::factory()
            ->count(3)
            ->state(new Sequence(
                ['name' => 'test', 'value' => 'value'],
                ['name' => 'test2', 'value' => 'value2'],
                ['name' => 'test3', 'value' => 'encrypted', 'is_secure' => true],
            ))
            ->for($this->currentStore)
            ->create();

        $storeSettings = StoreSettings::from(StoreSettings::empty());
        $storeSettings->settings = StoreSettingValue::builder()
            ->count(5)
            ->state(new Sequence(
                ['name' => 'test', 'value' => 'newvalue'],
                ['name' => 'test2', 'value' => 'value2'],
                ['name' => 'test3', 'value' => 'still_encrypted'],
                ['name' => 'test4', 'value' => 'value4'],
                ['name' => 'test5', 'value' => 'encrypted', 'is_secure' => true],
            ))
            ->create();

        $body = $storeSettings->getOriginal();
        $expectedResponse = $storeSettings->toArray();

        unset(
            $expectedResponse['settings']['test3'],
            $expectedResponse['settings']['test5']
        );

        $response = $this->patchJson('/api/stores/settings', $body, $this->headers);
        $response->assertStatus(200);

        $this->assertDatabaseHas('store_settings', ['name' => 'test', 'value' => 'newvalue']);
        $this->assertDatabaseHas('store_settings', ['name' => 'test2', 'value' => 'value2']);
        $this->assertDatabaseHas('store_settings', ['name' => 'test3', 'is_secure' => true]);
        $this->assertDatabaseMissing('store_settings', ['name' => 'test3', 'value' => 'still_encrypted', 'is_secure' => true]);
        $this->assertDatabaseHas('store_settings', ['name' => 'test4', 'value' => 'value4']);
        $this->assertDatabaseHas('store_settings', ['name' => 'test5', 'is_secure' => true]);
        $this->assertDatabaseMissing('store_settings', ['name' => 'test5', 'value' => 'encrypted', 'is_secure' => true]);

        $response->assertJsonStructure(['settings' => ['test' => ['name', 'value'], 'test2' => ['name', 'value'], 'test4' => ['name', 'value']]]);
        $response->assertJson($expectedResponse);
        $response->assertJsonFragment($expectedResponse);
        $this->assertIsArray($response['settings']);
        $this->assertCount(3, $response['settings']);
    }
}
