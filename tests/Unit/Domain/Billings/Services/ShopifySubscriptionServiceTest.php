<?php
declare(strict_types=1);

namespace Tests\Unit\Domain\Billings\Services;

use App;
use App\Domain\Billings\Services\ShopifySubscriptionService;
use App\Domain\Billings\Values\Subscription as SubscriptionValue;
use App\Domain\Billings\Values\UsageConfig;
use App\Domain\Shared\Exceptions\Shopify\ShopifyMutationClientException;
use App\Domain\Shared\Services\ShopifyGraphqlService;
use App\Domain\Shopify\Values\JwtPayload;
use App\Domain\Stores\Models\Store;
use Brick\Money\Money;
use Carbon\CarbonImmutable;
use Config;
use Firebase\JWT\JWT;
use Illuminate\Http\Client\Request;
use Illuminate\Support\Facades\Date;
use Illuminate\Support\Facades\Http;
use PHPUnit\Framework\Attributes\DataProvider;
use Tests\Fixtures\Domains\Billings\Traits\ShopifyAppSubscriptionCreateResponsesTestData;
use Tests\Fixtures\Domains\Billings\Traits\ShopifyQueryAppSubscriptionResponsesTestData;
use Tests\Fixtures\Domains\Shared\Shopify\Traits\ShopifyErrorsTestData;
use Tests\TestCase;

class ShopifySubscriptionServiceTest extends TestCase
{
    use ShopifyAppSubscriptionCreateResponsesTestData;
    use ShopifyErrorsTestData;
    use ShopifyQueryAppSubscriptionResponsesTestData;

    protected Store $currentStore;
    protected ShopifySubscriptionService $shopifySubscriptionService;
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

        $this->shopifySubscriptionService = resolve(ShopifySubscriptionService::class);
    }

    #[DataProvider('shopifyErrorExceptionsForMutationProvider')]
    public function testItDoesNotCreateShopifySubscriptionOnError(
        array $responseJson,
        int $httpStatusCode,
        string $expectedException,
    ): void {
        $subscriptionValue = $this->errorCaseSetup($responseJson, $httpStatusCode, $expectedException);
        $this->shopifySubscriptionService->create($subscriptionValue, $this->currentStore->domain);
        $this->validate();
    }

    public function testItDoesNotCreateShopifySubscriptionOnUserError(): void
    {
        $subscriptionValue = $this->errorCaseSetup(
            static::getShopifyAppSubscriptionCreateErrorResponse(),
            200,
            ShopifyMutationClientException::class,
        );
        $this->shopifySubscriptionService->create($subscriptionValue, $this->currentStore->domain);
        $this->validate();
    }

    public function testItCreatesShopifySubscription(): void
    {
        $subscriptionValue = SubscriptionValue::builder()->create([
            'store_id' => $this->currentStore->id,
        ]);

        Http::fake([
            App::context()->store->domain . '/admin/api/*/graphql.json' => Http::sequence()
                ->push(static::getShopifyAppSubscriptionCreateSuccessResponse()),
        ]);

        $expectedSubscriptionValue = SubscriptionValue::builder()->withShopifyData()->create([
            'store_id' => $this->currentStore->id,
        ]);

        $subscriptionActual = $this->shopifySubscriptionService->create($subscriptionValue, $this->currentStore->domain);

        $this->assertEquals($expectedSubscriptionValue->storeId, $subscriptionActual->storeId);
        $this->assertEquals($expectedSubscriptionValue->shopifyAppSubscriptionId, $subscriptionActual->shopifyAppSubscriptionId);
        $this->assertEquals($expectedSubscriptionValue->shopifyConfirmationUrl, $subscriptionActual->shopifyConfirmationUrl);
        $this->assertEquals($expectedSubscriptionValue->status, $subscriptionActual->status);
        $this->assertEquals($expectedSubscriptionValue->currentPeriodEnd, $subscriptionActual->currentPeriodEnd);
        $this->assertEquals($expectedSubscriptionValue->isTest, $subscriptionActual->isTest);
        $this->assertEquals($expectedSubscriptionValue->trialDays, $subscriptionActual->trialDays);
        $this->assertEquals($expectedSubscriptionValue->trialPeriodEnd, $subscriptionActual->trialPeriodEnd);
        $this->assertEquals($expectedSubscriptionValue->activatedAt, $subscriptionActual->activatedAt);
        $this->assertEquals($expectedSubscriptionValue->deactivatedAt, $subscriptionActual->deactivatedAt);

        foreach ($expectedSubscriptionValue->subscriptionLineItems as $i => $expectedLineItem) {
            $actualLineItem = $subscriptionActual->subscriptionLineItems[$i];

            $this->assertNotEmpty($actualLineItem->id);
            $this->assertNotEmpty($actualLineItem->subscriptionId);
            $this->assertEquals($expectedLineItem->shopifyAppSubscriptionId, $actualLineItem->shopifyAppSubscriptionId);
            $this->assertEquals($expectedLineItem->shopifyAppSubscriptionLineItemId, $actualLineItem->shopifyAppSubscriptionLineItemId);
            $this->assertEquals($expectedLineItem->type, $actualLineItem->type);
            $this->assertEquals($expectedLineItem->terms, $actualLineItem->terms);
            $this->assertEquals($expectedLineItem->recurringAmount, $actualLineItem->recurringAmount);
            $this->assertEquals($expectedLineItem->recurringAmountCurrency, $actualLineItem->recurringAmountCurrency);
            $this->assertEquals($expectedLineItem->usageCappedAmount, $actualLineItem->usageCappedAmount);
            $this->assertEquals($expectedLineItem->usageCappedAmountCurrency, $actualLineItem->usageCappedAmountCurrency);
        }

        $this->validate();
    }

    public function testItAddsUsage(): void
    {
        Http::fake([
            App::context()->store->domain . '/admin/api/*/graphql.json' => Http::sequence()
                ->push(
                    '{
                      "data": {
                        "appSubscriptionCreate": {
                          "userErrors": [],
                          "confirmationUrl": "https://{shop}.myshopify.com/admin/charges/4028497976/confirm_recurring_application_charge?signature=BAh7BzoHaWRsKwc4AB7wOhJhdXRvX2FjdGl2YXRlVA%3D%3D--987b3537018fdd69c50f13d6cbd3fba468e0e9a6",
                          "appSubscription": {
                            "id": "gid://shopify/AppSubscription/4028497976",
                            "lineItems": [
                              {
                                "id": "gid://shopify/AppSubscriptionLineItem/4028497976?v=1&index=0",
                                "plan": {
                                  "pricingDetails": {
                                    "__typename": "AppRecurringPricing"
                                  }
                                }
                              },
                              {
                                "id": "gid://shopify/AppSubscriptionLineItem/4028497976?v=1&index=1",
                                "plan": {
                                  "pricingDetails": {
                                    "__typename": "AppUsagePricing"
                                  }
                                }
                              }
                            ]
                          }
                        }
                      }
                    }'
                ),
        ]);

        $usageConfig = UsageConfig::from([
            'storeId' => '1',
            'description' => 'Test',
            'currency' => 'USD',
            'config' => json_decode(<<<'EOF'
                    [
                      {
                        "description": "First $2500 included in subscription fee",
                        "start": 0,
                        "end": 250000,
                        "step": 250000,
                        "price": 0,
                        "currency": "USD"
                      },
                      {
                        "description": "$2500.01 - $100,000 at $100/$2500 (4%)",
                        "start": 250001,
                        "end": 10000000,
                        "step": 250000,
                        "price": 10000,
                        "currency": "USD"
                      },
                      {
                        "description": ">$200,000.01 at $50/$2500 (2%)",
                        "start": 20000001,
                        "end": null,
                        "step": 250000,
                        "price": 5000,
                        "currency": "USD"
                      },
                      {
                        "description": "$100,000.01 - $200,000 at $800/$25000 (3.2%)",
                        "start": 10000001,
                        "end": 20000000,
                        "step": 2500000,
                        "price": 80000,
                        "currency": "USD"
                      }
                    ]
                    EOF, true, 512, JSON_THROW_ON_ERROR),
            'subscriptionLineItemId' => '1',
            'validFrom' => Date::now()->subDay(),
            'validTo' => null,
        ]);

        resolve(ShopifySubscriptionService::class)->addUsage('Test', Money::of(100, 'USD'), $usageConfig);

        Http::assertSent(function (Request $request) {
            $body = json_decode($request->body(), true);
            $this->assertEquals('Test', $body['variables']['description']);
            $this->assertEquals(100, $body['variables']['amount']);
            $this->assertEquals('USD', $body['variables']['currency']);
            $this->assertEquals('1', $body['variables']['subscriptionLineItemId']);

            return true;
        });
    }

    #[DataProvider('shopifyErrorExceptionsProvider')]
    public function testItDoesNotGetCurrentPeriodEndByAppSubscriptionIdOnError(
        array $responseJson,
        int $httpStatusCode,
        string $expectedException,
    ): void {
        Http::fake([
            App::context()->store->domain . '/admin/api/*/graphql.json' => Http::sequence()
                ->push($responseJson, $httpStatusCode),
        ]);
        $this->expectException($expectedException);

        $currentPeriodEnd = $this->shopifySubscriptionService->getCurrentPeriodEndByAppSubscriptionId(
            'gid://shopify/AppSubscription/4028497976'
        );

        $this->assertNull($currentPeriodEnd);

        Http::assertSent(function (Request $request) {
            $body = json_decode($request->body(), true);
            $this->assertEquals('gid://shopify/AppSubscription/4028497976', $body['variables']['id']);

            return $request->method() === 'POST' &&
                $request->hasHeader('X-Shopify-Access-Token', App::context()->store->accessToken);
        });
    }

    public function testItDoesNotGetCurrentPeriodEndByAppSubscriptionIdOnNotFound(): void
    {
        Http::fake([
            App::context()->store->domain . '/admin/api/*/graphql.json' => Http::sequence()
                ->push(static::getShopifyQueryAppSubscriptionNullResponse(), 200),
        ]);

        $currentPeriodEnd = $this->shopifySubscriptionService->getCurrentPeriodEndByAppSubscriptionId(
            'gid://shopify/AppSubscription/4028497976'
        );

        $this->assertNull($currentPeriodEnd);

        Http::assertSent(function (Request $request) {
            $body = json_decode($request->body(), true);
            $this->assertEquals('gid://shopify/AppSubscription/4028497976', $body['variables']['id']);

            return $request->method() === 'POST' &&
                $request->hasHeader('X-Shopify-Access-Token', App::context()->store->accessToken);
        });
    }

    public function testItGetsNullCurrentPeriodEndByAppSubscriptionId(): void
    {
        Http::fake([
            App::context()->store->domain . '/admin/api/*/graphql.json' => Http::sequence()
                ->push(static::getShopifyQueryAppSubscriptionNullCurrentPeriodEndSuccessResponse()),
        ]);

        $currentPeriodEnd = $this->shopifySubscriptionService->getCurrentPeriodEndByAppSubscriptionId(
            'gid://shopify/AppSubscription/4028497976'
        );

        $this->assertNull($currentPeriodEnd);

        Http::assertSent(function (Request $request) {
            $body = json_decode($request->body(), true);
            $this->assertEquals('gid://shopify/AppSubscription/4028497976', $body['variables']['id']);

            return $request->method() === 'POST' &&
                $request->hasHeader('X-Shopify-Access-Token', App::context()->store->accessToken);
        });
    }

    public function testItGetsCurrentPeriodEndByAppSubscriptionId(): void
    {
        Http::fake([
            App::context()->store->domain . '/admin/api/*/graphql.json' => Http::sequence()
                ->push(static::getShopifyQueryAppSubscriptionCurrentPeriodEndSuccessResponse()),
        ]);

        $currentPeriodEnd = $this->shopifySubscriptionService->getCurrentPeriodEndByAppSubscriptionId(
            'gid://shopify/AppSubscription/4028497976'
        );

        $this->assertEquals(new CarbonImmutable('2024-02-19T17:00:00Z'), $currentPeriodEnd);

        Http::assertSent(function (Request $request) {
            $body = json_decode($request->body(), true);
            $this->assertEquals('gid://shopify/AppSubscription/4028497976', $body['variables']['id']);

            return $request->method() === 'POST' &&
                $request->hasHeader('X-Shopify-Access-Token', App::context()->store->accessToken);
        });
    }

    public function testItGetsCurrentPeriodEndByAppSubscriptionIdWithMockedShopifyGraphqlService(): void
    {
        $this->mock(ShopifyGraphqlService::class, function ($mock) {
            $mock->shouldReceive('post')
                ->once()
                ->with(
                    <<<'QUERY'
                    query ($id: ID!) {
                      node(id: $id) {
                        ...on AppSubscription {
                          id
                          currentPeriodEnd
                        }
                      }
                    }
                    QUERY,
                    [
                        'id' => 'gid://shopify/AppSubscription/1',
                    ]
                )
                ->andReturn(static::getShopifyQueryAppSubscriptionCurrentPeriodEndSuccessResponse(
                    'gid://shopify/AppSubscription/1',
                    '2024-04-09T22:19:42Z'
                ));
        });

        $shopifySubscriptionService = resolve(ShopifySubscriptionService::class);
        $currentPeriodEnd = $shopifySubscriptionService->getCurrentPeriodEndByAppSubscriptionId(
            'gid://shopify/AppSubscription/1'
        );

        $this->assertEquals(new CarbonImmutable('2024-04-09T22:19:42Z'), $currentPeriodEnd);
    }

    private function errorCaseSetup(
        array $responseJson,
        int $httpStatusCode,
        string $expectedException,
    ): SubscriptionValue {
        Http::fake([
            App::context()->store->domain . '/admin/api/*/graphql.json' => Http::sequence()
                ->push($responseJson, $httpStatusCode),
        ]);
        $this->expectException($expectedException);

        return SubscriptionValue::builder()->create([
            'store_id' => $this->currentStore->id,
        ]);
    }

    private function validate(): void
    {
        Http::assertSent(function (Request $request) {
            // TODO: validate graphql query
            return $request->method() === 'POST' &&
                $request->hasHeader('X-Shopify-Access-Token', App::context()->store->accessToken);
        });
    }
}
