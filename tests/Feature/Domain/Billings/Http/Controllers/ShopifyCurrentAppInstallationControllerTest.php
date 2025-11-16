<?php
declare(strict_types=1);

namespace Tests\Feature\Domain\Billings\Http\Controllers;

use App;
use App\Domain\Shopify\Values\JwtPayload;
use App\Domain\Stores\Models\Store;
use App\Domain\Stores\Values\Store as StoreValue;
use Config;
use Firebase\JWT\JWT;
use Illuminate\Http\Client\Request;
use Illuminate\Support\Facades\Http;
use Tests\Fixtures\Domains\Billings\Traits\ShopifyCurrentAppInstallationResponsesTestData;
use Tests\Fixtures\Domains\Shared\Shopify\Traits\ShopifyErrorsTestData;
use Tests\TestCase;

class ShopifyCurrentAppInstallationControllerTest extends TestCase
{
    use ShopifyCurrentAppInstallationResponsesTestData;
    use ShopifyErrorsTestData;

    private Store $currentStore;
    private array $headers;

    const SUBSCRIPTIONS_API_URL = '/api/stores/billings/subscriptions/shopify_current_app_installation';

    protected function setUp(): void
    {
        parent::setUp();

        $this->currentStore = Store::withoutEvents(function () {
            return Store::factory()->create();
        });
        App::context(store: StoreValue::from($this->currentStore));

        Config::set('services.shopify.client_secret', 'super-secret-key');

        $this->headers = [
            'Authorization' => 'Bearer ' . JWT::encode(
                (new JwtPayload(domain: $this->currentStore->domain))->toArray(),
                config('services.shopify.client_secret'),
                'HS256'
            ),
        ];
    }

    public function testItReturnsUnauthorizedErrorWhenNoStoreContextSentOnGet(): void
    {
        App::context(store: StoreValue::from(StoreValue::empty()));
        $this->headers = [];

        $this->getJson(static::SUBSCRIPTIONS_API_URL, $this->headers)
            ->assertStatus(401);
    }

    public function testItGetsShopifyCurrentAppInstallation(): void
    {
        Http::fake([
            App::context()->store->domain . '/admin/api/*/graphql.json' => Http::sequence()
                ->push($this->getShopifyCurrentAppInstallationQuerySuccessResponse()),
        ]);

        $response = $this->getJson(static::SUBSCRIPTIONS_API_URL, $this->headers);
        $response->assertStatus(200);
        $response->assertJsonStructure([
            'shopify_current_app_installation' => [
                'id',
                'active_subscriptions',
            ],
        ]);

        $expectedJson = [
            'shopify_current_app_installation' => [
                'id' => 'gid://shopify/AppInstallation/432744464523',
                'active_subscriptions' => [
                    [
                        'id' => 'gid://shopify/AppSubscription/1234567890',
                        'status' => 'active',
                    ],
                ],
            ],
        ];
        $response->assertJsonFragment($expectedJson);

        Http::assertSent(function (Request $request) {
            // TODO: validate graphql query
            return $request->method() === 'POST' &&
                $request->hasHeader('X-Shopify-Access-Token', App::context()->store->accessToken);
        });
    }
}
