<?php
declare(strict_types=1);

namespace Tests\Feature\Domain\Products\Http\Controllers;

use App;
use App\Domain\Shopify\Values\JwtPayload;
use App\Domain\Stores\Models\Store;
use Config;
use Firebase\JWT\JWT;
use Illuminate\Support\Facades\Http;
use Tests\TestCase;

class ProductControllerTest extends TestCase
{
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

    public function testItGetsVariants(): void
    {
        $responseData = [
            'data' => [
                'product' => [
                    'title' => 'Example Product',
                    'description' => 'This is an example product description.',
                    'onlineStoreUrl' => 'https://example-store.myshopify.com/products/example-product',
                    'variants' => [
                        'edges' => [
                            [
                                'node' => [
                                    'id' => 'gid://shopify/ProductVariant/123456789',
                                    'displayName' => 'Variant 1',
                                ],
                            ],
                            [
                                'node' => [
                                    'id' => 'gid://shopify/ProductVariant/987654321',
                                    'displayName' => 'Variant 2',
                                ],
                            ],
                        ],
                    ],
                ],
            ],
        ];
        Http::fake([
            App::context()->store->domain . '/admin/api/*/graphql.json' => Http::sequence()
                ->push($responseData),
        ]);

        $response = $this->getJson(
            '/api/stores/products/123',
            $this->headers
        );

        $response->assertStatus(200);
        $response->assertExactJson($responseData);
    }
}
