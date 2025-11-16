<?php
declare(strict_types=1);

namespace Feature\Domain\Billings\Http\Controllers;

use App;
use App\Domain\Billings\Models\Subscription;
use App\Domain\Billings\Values\Subscription as SubscriptionValue;
use App\Domain\Billings\Values\SubscriptionLineItem as SubscriptionLineItemValue;
use App\Domain\Shopify\Values\JwtPayload;
use App\Domain\Stores\Models\Store;
use App\Domain\Stores\Values\Store as StoreValue;
use App\Enums\Exceptions\ApiExceptionErrorCodes;
use App\Enums\Exceptions\ApiExceptionTypes;
use Config;
use Firebase\JWT\JWT;
use Illuminate\Http\Client\Request;
use Illuminate\Support\Facades\Http;
use PHPUnit\Framework\Attributes\DataProvider;
use Tests\Fixtures\Domains\Billings\Traits\ShopifyAppSubscriptionCreateResponsesTestData;
use Tests\Fixtures\Domains\Shared\Shopify\Traits\ShopifyErrorsTestData;
use Tests\TestCase;

class SubscriptionsControllerTest extends TestCase
{
    use ShopifyAppSubscriptionCreateResponsesTestData;
    use ShopifyErrorsTestData;

    private Store $currentStore;
    private array $headers;

    const SUBSCRIPTIONS_API_URL = '/api/stores/billings/subscriptions';

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

    public function testItReturnsUnauthorizedErrorWhenNoStoreContextSentOnPost(): void
    {
        App::context(store: StoreValue::from(StoreValue::empty()));
        $this->headers = [];

        $subscriptionValue = SubscriptionValue::builder()->create(['store_id' => $this->currentStore->id]);
        $this->postJson(static::SUBSCRIPTIONS_API_URL, $subscriptionValue->toArray(), $this->headers)
            ->assertStatus(401);
    }

    #[DataProvider('shopifyErrorApiResponsesProvider')]
    public function testItDoesNotCreateSubscriptionOnShopifyError(
        array $responseJson,
        int $httpStatusCode,
    ): void {
        Http::fake([
            App::context()->store->domain . '/admin/api/*/graphql.json' => Http::sequence()
                ->push($responseJson, $httpStatusCode),
        ]);

        $subscriptionValue = SubscriptionValue::builder()->create([
            'store_id' => $this->currentStore->id,
        ]);

        $response = $this->postJson(static::SUBSCRIPTIONS_API_URL, $subscriptionValue->toArray(), $this->headers);
        $response->assertStatus(500);
        $response->assertJsonFragment([
            'type' => ApiExceptionTypes::API_ERROR->value,
            'code' => ApiExceptionErrorCodes::SERVER_ERROR->value,
            'message' => 'Internal call to Shopify failed, please try again in a few minutes.',
            'errors' => [],
        ]);

        Http::assertSent(function (Request $request) {
            // TODO: validate graphql query
            return $request->method() === 'POST' &&
                $request->hasHeader('X-Shopify-Access-Token', App::context()->store->accessToken);
        });
    }

    public static function shopifyErrorApiResponsesProvider(): array
    {
        return [
            'On HTTP request error' => [
                ['errors' => '400 error'],
                400,
            ],
            'On HTTP server error' => [
                ['errors' => '500 error'],
                500,
            ],
            'On Shopify authentication error' => [
                static::getShopifyAdminApiAuthenticationErrorResponse(),
                401,
            ],
            'On Shopify mutation server error' => [
                static::getShopifyAdminApiErrorResponse(),
                500,
            ],
            'On Shopify mutation user error' => [
                static::getShopifyAppSubscriptionCreateErrorResponse(),
                400,
            ],
        ];
    }

    public function testItCreatesSubscription(): void
    {
        $subscriptionValue = SubscriptionValue::builder()->create([
            'store_id' => $this->currentStore->id,
        ]);

        Http::fake([
            App::context()->store->domain . '/admin/api/*/graphql.json' => Http::sequence()
                ->push($this->getShopifyAppSubscriptionCreateSuccessResponse()),
        ]);

        $response = $this->postJson(static::SUBSCRIPTIONS_API_URL, $subscriptionValue->toArray(), $this->headers);

        $response->assertStatus(201);

        $actualResponse = $response->json();

        $response->assertJsonStructure([
            'subscription' => [
                'id',
                'store_id',
                'shopify_app_subscription_id',
                'shopify_confirmation_url',
                'status',
                'activated_at',
                'deactivated_at',
                'subscription_line_items',
            ],
        ]);

        $expectedLineItems = [];
        foreach ($actualResponse['subscription']['subscription_line_items'] as $lineItem) {
            $expectedLineItems[] = SubscriptionLineItemValue::builder()->create([
                'id' => $lineItem['id'],
                'subscription_id' => $lineItem['subscription_id'],
                'shopify_app_subscription_id' => $lineItem['shopify_app_subscription_id'],
                'shopify_app_subscription_line_item_id' => $lineItem['shopify_app_subscription_line_item_id'],
                'type' => $lineItem['type'],
                'terms' => $lineItem['terms'],
                'recurring_amount' => $lineItem['recurring_amount'],
                'recurring_amount_currency' => $lineItem['recurring_amount_currency'],
                'usage_capped_amount' => $lineItem['usage_capped_amount'],
                'usage_capped_amount_currency' => $lineItem['usage_capped_amount_currency'],
            ]);
        }
        $this->assertCount(2, $expectedLineItems);

        $expectedSubscriptionValue = SubscriptionValue::builder()->create([
            'id' => $actualResponse['subscription']['id'],
            'store_id' => $this->currentStore->id,
            'shopify_app_subscription_id' => $actualResponse['subscription']['shopify_app_subscription_id'],
            'shopify_confirmation_url' => $actualResponse['subscription']['shopify_confirmation_url'],
            'status' => $actualResponse['subscription']['status'],
            'activated_at' => $actualResponse['subscription']['activated_at'],
            'deactivated_at' => $actualResponse['subscription']['deactivated_at'],
            'subscription_line_items' => $expectedLineItems,
        ])->toArray();

        $response->assertJsonFragment($expectedSubscriptionValue);

        Http::assertSent(function (Request $request) {
            // TODO: validate graphql query
            return $request->method() === 'POST' &&
                $request->hasHeader('X-Shopify-Access-Token', App::context()->store->accessToken);
        });

        $expectedSubscriptionDatabaseValue = $expectedSubscriptionValue;
        unset($expectedSubscriptionDatabaseValue['subscription_line_items']);
        $this->assertDatabaseHas('billings_subscriptions', $expectedSubscriptionDatabaseValue);

        foreach ($actualResponse['subscription']['subscription_line_items'] as $lineItem) {
            $this->assertDatabaseHas('billings_subscription_line_items', $lineItem);
        }
    }

    public function testItGetsActiveSubscription(): void
    {
        Subscription::factory()->withShopifyData()->cancelled()->create(['store_id' => $this->currentStore->id]);
        Subscription::factory()->withShopifyData()->declined()->create(['store_id' => $this->currentStore->id]);
        Subscription::factory()->withShopifyData()->expired()->create(['store_id' => $this->currentStore->id]);
        Subscription::factory()->withShopifyData()->frozen()->create(['store_id' => $this->currentStore->id]);
        Subscription::factory()->withShopifyData()->pending()->create(['store_id' => $this->currentStore->id]);
        $subscription = Subscription::factory()->withShopifyData()->active()->create(['store_id' => $this->currentStore->id]);

        $response = $this->getJson(static::SUBSCRIPTIONS_API_URL . '/active', $this->headers);
        $response->assertStatus(200);

        $actualSubscription = SubscriptionValue::from($response->json()['subscription']);

        $this->assertEquals($subscription->id, $actualSubscription->id);
        $this->assertEquals($subscription->store_id, $actualSubscription->storeId);
        $this->assertEquals($subscription->shopify_app_subscription_id, $actualSubscription->shopifyAppSubscriptionId);
        $this->assertEquals($subscription->shopify_confirmation_url, $actualSubscription->shopifyConfirmationUrl);
        $this->assertEquals($subscription->status, $actualSubscription->status);
        $this->assertEquals($subscription->activated_at, $actualSubscription->activatedAt);
        $this->assertEquals($subscription->deactivated_at, $actualSubscription->deactivatedAt);
    }

    public function testItErrorsWithNoActiveSubscription(): void
    {
        Subscription::factory()->withShopifyData()->cancelled()->create(['store_id' => $this->currentStore->id]);
        Subscription::factory()->withShopifyData()->declined()->create(['store_id' => $this->currentStore->id]);
        Subscription::factory()->withShopifyData()->expired()->create(['store_id' => $this->currentStore->id]);
        Subscription::factory()->withShopifyData()->frozen()->create(['store_id' => $this->currentStore->id]);
        Subscription::factory()->withShopifyData()->pending()->create(['store_id' => $this->currentStore->id]);

        $response = $this->getJson(static::SUBSCRIPTIONS_API_URL . '/active', $this->headers);
        $response->assertStatus(404);
        $response->assertJsonFragment([
            'type' => 'request_error',
            'code' => 'resource_not_found',
            'message' => 'No active subscription found',
            'errors' => [],
        ]);
    }
}
