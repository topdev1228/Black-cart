<?php
declare(strict_types=1);

namespace Tests\Unit\Domain\Billings\Services;

use App;
use App\Domain\Billings\Enums\SubscriptionStatus;
use App\Domain\Billings\Services\ShopifyCurrentAppInstallationService;
use App\Domain\Billings\Values\ShopifyAppSubscription as ShopifyAppSubscriptionValue;
use App\Domain\Billings\Values\ShopifyCurrentAppInstallation as ShopifyCurrentAppInstallationValue;
use App\Domain\Shopify\Values\JwtPayload;
use App\Domain\Stores\Models\Store;
use Config;
use Firebase\JWT\JWT;
use Illuminate\Http\Client\Request;
use Illuminate\Support\Facades\Http;
use PHPUnit\Framework\Attributes\DataProvider;
use Tests\Fixtures\Domains\Billings\Traits\ShopifyCurrentAppInstallationResponsesTestData;
use Tests\Fixtures\Domains\Shared\Shopify\Traits\ShopifyErrorsTestData;
use Tests\TestCase;

class ShopifyCurrentAppInstallationServiceTest extends TestCase
{
    use ShopifyCurrentAppInstallationResponsesTestData;
    use ShopifyErrorsTestData;

    protected Store $currentStore;
    protected ShopifyCurrentAppInstallationService $shopifyCurrentAppInstallationService;
    protected array $headers;

    protected function setUp(): void
    {
        parent::setUp();

        $this->currentStore = Store::factory()->create();
        App::context(store: $this->currentStore);

        Config::set('services.shopify.client_secret', 'super-secret-key');

        $this->headers = [
            'Authorization' => 'Bearer ' . JWT::encode(
                (new JwtPayload(domain: $this->currentStore->domain))->toArray(),
                config('services.shopify.client_secret'),
                'HS256'
            ),
        ];

        $this->shopifyCurrentAppInstallationService = resolve(ShopifyCurrentAppInstallationService::class);
    }

    #[DataProvider('shopifyErrorExceptionsProvider')]
    public function testItDoesNotGetCurrentAppInstallationOnError(
        array $responseJson,
        int $httpStatusCode,
        string $expectedException,
    ): void {
        Http::fake([
            App::context()->store->domain . '/admin/api/*/graphql.json' => Http::sequence()
                ->push($responseJson, $httpStatusCode),
        ]);
        $this->expectException($expectedException);

        $this->shopifyCurrentAppInstallationService->get();

        Http::assertSent(function (Request $request) {
            // TODO: validate graphql query
            return $request->method() === 'POST' &&
                $request->hasHeader('X-Shopify-Access-Token', App::context()->store->accessToken);
        });
    }

    public function testItCreatesShopifySubscription(): void
    {
        Http::fake([
            App::context()->store->domain . '/admin/api/*/graphql.json' => Http::sequence()
                ->push(static::getShopifyCurrentAppInstallationQuerySuccessResponse()),
        ]);

        $expectedCurrentAppInstallation = new ShopifyCurrentAppInstallationValue(
            id: 'gid://shopify/AppInstallation/432744464523',
            activeSubscriptions: ShopifyAppSubscriptionValue::collection([
                new ShopifyAppSubscriptionValue(
                    id: 'gid://shopify/AppSubscription/1234567890',
                    status: SubscriptionStatus::ACTIVE,
                ),
            ]),
        );

        $actualCurrentAppInstallation = $this->shopifyCurrentAppInstallationService->get();

        $this->assertEquals($expectedCurrentAppInstallation->id, $actualCurrentAppInstallation->id);
        foreach ($actualCurrentAppInstallation->activeSubscriptions as $key => $activeSubscription) {
            $this->assertEquals($expectedCurrentAppInstallation->activeSubscriptions[$key]->id, $activeSubscription->id);
            $this->assertEquals($expectedCurrentAppInstallation->activeSubscriptions[$key]->status, $activeSubscription->status);
        }

        Http::assertSent(function (Request $request) {
            // TODO: validate graphql query
            return $request->method() === 'POST' &&
                $request->hasHeader('X-Shopify-Access-Token', App::context()->store->accessToken);
        });
    }
}
