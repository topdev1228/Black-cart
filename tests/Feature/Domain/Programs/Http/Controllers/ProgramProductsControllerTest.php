<?php
declare(strict_types=1);

namespace Tests\Feature\Domain\Programs\Http\Controllers;

use App;
use App\Domain\Programs\Models\Program;
use App\Domain\Shopify\Values\JwtPayload;
use App\Domain\Stores\Models\Store;
use App\Domain\Stores\Values\Store as StoreValue;
use Config;
use Firebase\JWT\JWT;
use Illuminate\Support\Facades\Http;
use Tests\Fixtures\Domains\Programs\Traits\ShopifySellingPlanGroupResponsesTestData;
use Tests\TestCase;

class ProgramProductsControllerTest extends TestCase
{
    use ShopifySellingPlanGroupResponsesTestData;

    private array $headers;

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
                (new JwtPayload(domain: $this->currentStore->domain))->toArray(),
                config('services.shopify.client_secret'),
                'HS256'
            ),
        ];
    }

    public function testItGetsProducts(): void
    {
        Http::fake([
            App::context()->store->domain . '/admin/api/*/graphql.json' => Http::sequence()
                ->push(static::getSellingPlanProductResponse()),
        ]);

        $program = Program::factory()->withShopifySellingPlanIds()->create(['store_id' => $this->currentStore->id]);
        $response = $this->getJson(
            '/api/stores/programs/' . $program->id . '/products',
            $this->headers
        );

        $response->assertStatus(200);
        $response->assertJsonStructure([]);
    }

    public function testItReturnsUnauthorizedErrorWhenNoStoreContextSet(): void
    {
        App::context(store: StoreValue::from(StoreValue::empty()));
        $this->headers = [];

        $response = $this->getJson('/api/stores/programs/1/products', $this->headers);

        $response->assertStatus(401);
        $response->assertJsonStructure(['message']);
    }
}
