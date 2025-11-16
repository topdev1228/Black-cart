<?php
declare(strict_types=1);

namespace Tests\Feature\Domain\Billings\Listeners;

use App;
use App\Domain\Billings\Enums\SubscriptionStatus;
use App\Domain\Billings\Listeners\WebhookShopifyAppSubscriptionsUpdateListener;
use App\Domain\Billings\Models\Subscription;
use App\Domain\Billings\Values\WebhookShopifyAppSubscription as WebhookShopifyAppSubscriptionValue;
use App\Domain\Billings\Values\WebhookShopifyAppSubscriptionsUpdate as WebhookShopifyAppSubscriptionsUpdateValue;
use App\Domain\Shopify\Values\JwtPayload;
use App\Domain\Shopify\Values\JwtToken;
use App\Domain\Stores\Models\Store;
use App\Domain\Stores\Values\Store as StoreValue;
use Carbon\CarbonImmutable;
use Config;
use Firebase\JWT\JWT;
use Illuminate\Support\Facades\Http;
use PHPUnit\Framework\Attributes\DataProvider;
use Tests\Fixtures\Domains\Billings\Traits\ShopifyQueryAppSubscriptionResponsesTestData;
use Tests\Fixtures\Domains\Billings\Traits\WebhookShopifyAppSubscriptionsUpdateTestData;
use Tests\TestCase;

class WebhookShopifyAppSubscriptionsUpdateListenerTest extends TestCase
{
    use ShopifyQueryAppSubscriptionResponsesTestData;
    use WebhookShopifyAppSubscriptionsUpdateTestData;

    protected Store $store;

    protected function setUp(): void
    {
        parent::setUp();

        Config::set('services.shopify.client_secret', 'super-secret-key');

        $this->store = Store::withoutEvents(function () {
            return Store::factory()->create();
        });

        App::context(
            store: StoreValue::from($this->store),
            jwtToken: new JwtToken(JWT::encode(
                (new JwtPayload(domain: $this->store->domain))->toArray(),
                config('services.shopify.client_secret'),
                'HS256'
            ))
        );
    }

    #[DataProvider('getAppSubscriptionsUpdateAllStatusesProvider')]
    public function testItHandlesWebhook(
        string $shopifyAppSubscriptionId,
        SubscriptionStatus $newStatus,
        array $data,
        string $timestampColumn,
        string $timestampValue
    ): void {
        if ($newStatus === SubscriptionStatus::ACTIVE) {
            Http::fake([
                App::context()->store->domain . '/admin/api/*/graphql.json' => Http::sequence()
                    ->push(static::getShopifyQueryAppSubscriptionCurrentPeriodEndSuccessResponse(
                        $shopifyAppSubscriptionId,
                        '2024-05-29T17:00:00Z',
                    )),
            ]);
        }

        $subscription = Subscription::factory()->withShopifyData()->create([
            'store_id' => App::context()->store->id,
            'shopify_app_subscription_id' => $shopifyAppSubscriptionId,
            'trial_days' => 30,
        ]);

        $webhookShopifyAppSubscriptionsUpdateValue = WebhookShopifyAppSubscriptionsUpdateValue::builder()->create([
            'appSubscription' => WebhookShopifyAppSubscriptionValue::from($data),
        ]);

        $webhookHandler = resolve(WebhookShopifyAppSubscriptionsUpdateListener::class);
        /** @var WebhookShopifyAppSubscriptionsUpdateListener $webhookHandler */
        $webhookHandler->handle($webhookShopifyAppSubscriptionsUpdateValue);

        if ($timestampColumn === '' && $timestampValue === '') {
            // Don't validate timestamp column values
            $this->assertDatabaseHas('billings_subscriptions', [
                'id' => $subscription->id,
                'shopify_app_subscription_id' => $shopifyAppSubscriptionId,
                'status' => $newStatus->value,
            ]);
        } else {
            if ($newStatus === SubscriptionStatus::ACTIVE) {
                // Validate current_period_end too
                $this->assertDatabaseHas('billings_subscriptions', [
                    'id' => $subscription->id,
                    'shopify_app_subscription_id' => $shopifyAppSubscriptionId,
                    'status' => $newStatus->value,
                    $timestampColumn => $timestampValue,
                    'current_period_end' => '2024-05-29 17:00:00',
                    'trial_period_end' => (new CarbonImmutable($timestampValue))->addDays(30)->toDateTimeString(),
                ]);
            } else {
                $this->assertDatabaseHas('billings_subscriptions', [
                    'id' => $subscription->id,
                    'shopify_app_subscription_id' => $shopifyAppSubscriptionId,
                    'status' => $newStatus->value,
                    $timestampColumn => $timestampValue,
                ]);
            }
        }
    }

    public function testItHandlesWebhookUpdateCurrentPeriodOnActiveSubscription(): void
    {
        $subscription = Subscription::factory()->withShopifyData()->active()->create([
            'store_id' => App::context()->store->id,
        ]);

        Http::fake([
            App::context()->store->domain . '/admin/api/*/graphql.json' => Http::sequence()
                ->push(static::getShopifyQueryAppSubscriptionCurrentPeriodEndSuccessResponse(
                    $subscription->shopify_app_subscription_id,
                    '2024-05-29T17:00:00Z',
                )),
        ]);

        $webhookShopifyAppSubscriptionsUpdateValue = WebhookShopifyAppSubscriptionsUpdateValue::builder()->create([
            'appSubscription' => WebhookShopifyAppSubscriptionValue::builder()->create([
                'id' => $subscription->shopify_app_subscription_id,
                'status' => SubscriptionStatus::ACTIVE,
            ]),
        ]);

        $webhookHandler = resolve(WebhookShopifyAppSubscriptionsUpdateListener::class);
        /** @var WebhookShopifyAppSubscriptionsUpdateListener $webhookHandler */
        $webhookHandler->handle($webhookShopifyAppSubscriptionsUpdateValue);

        // Validate current_period_end too
        $this->assertDatabaseHas('billings_subscriptions', [
            'id' => $subscription->id,
            'shopify_app_subscription_id' => $subscription->shopify_app_subscription_id,
            'status' => SubscriptionStatus::ACTIVE->value,
            'current_period_end' => '2024-05-29 17:00:00',
        ]);
    }
}
