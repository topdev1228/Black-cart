<?php
declare(strict_types=1);

namespace Tests\Unit\Domain\Billings\Services;

use App;
use App\Domain\Billings\Models\Subscription;
use App\Domain\Billings\Models\SubscriptionLineItem;
use App\Domain\Billings\Models\UsageConfig;
use App\Domain\Billings\Services\UsageConfigService;
use App\Domain\Billings\Values\Subscription as SubscriptionValue;
use App\Domain\Billings\Values\UsageConfig as UsageConfigValue;
use App\Domain\Stores\Models\Store;
use App\Domain\Stores\Values\Store as StoreValue;
use Illuminate\Support\Facades\Date;
use Tests\TestCase;

class UsageConfigServiceTest extends TestCase
{
    protected Store $currentStore;
    protected UsageConfigService $usageConfigService;

    protected function setUp(): void
    {
        parent::setUp();

        $this->currentStore = Store::withoutEvents(function () {
            return Store::factory()->create();
        });
        App::context(store: StoreValue::from($this->currentStore));

        $this->usageConfigService = resolve(UsageConfigService::class);
    }

    public function testItCreatesUsageConfig(): void
    {
        $config = UsageConfigValue::builder()->create(['store_id' => $this->currentStore->id]);
        $this->usageConfigService->create($config);

        $modelConfig = UsageConfig::first();
        $this->assertEquals($config->storeId, $modelConfig->store_id);
        $this->assertEquals($config->validFrom?->toDateTimeString(), $modelConfig->valid_from?->toDateTimeString());
        $this->assertEquals($config->validTo?->toDateTimeString(), $modelConfig->valid_to?->toDateTimeString());
        $this->assertEquals($config->config->toArray(), $modelConfig->config);
        $this->assertEquals($config->subscriptionLineItemId, $modelConfig->subscription_line_item_id);
        $this->assertEquals($config->description, $modelConfig->description);
        $this->assertEquals($config->currency, $modelConfig->currency);
    }

    public function testItGetsLatestUsageConfig(): void
    {
        $validConfig = UsageConfigValue::builder()->create([
            'store_id' => $this->currentStore->id,
            'valid_from' => Date::now()->subDay(),
            'valid_to' => null,
        ]);
        $this->usageConfigService->create($validConfig);
        $this->usageConfigService->create(UsageConfigValue::builder()->create([
            'store_id' => $this->currentStore->id,
            'valid_from' => Date::now()->addDay(),
            'valid_to' => Date::now()->addDays(7),
        ]));
        $this->usageConfigService->create(UsageConfigValue::builder()->create([
            'store_id' => $this->currentStore->id,
            'valid_from' => Date::now()->subDays(3),
            'valid_to' => Date::now()->addDays(3),
        ]));

        $latestConfig = $this->usageConfigService->getLatestConfig();
        $this->assertEquals($validConfig->storeId, $latestConfig->storeId);
        $this->assertEquals($validConfig->validFrom?->toDateTimeString(), $latestConfig->validFrom?->toDateTimeString());
        $this->assertEquals($validConfig->validTo?->toDateTimeString(), $latestConfig->validTo?->toDateTimeString());
        $this->assertEquals($validConfig->config->toArray(), $latestConfig->config->toArray());
        $this->assertEquals($validConfig->subscriptionLineItemId, $latestConfig->subscriptionLineItemId);
        $this->assertEquals($validConfig->description, $latestConfig->description);
        $this->assertEquals($validConfig->currency, $latestConfig->currency);
    }

    public function testItDoesNotCreatesUsageConfigForSubscriptionOnNonActiveSubscription(): void
    {
        /**
         * @var Subscription $subscription
         */
        $subscription = Subscription::factory()->withShopifyData()->expired()->create([
            'store_id' => $this->currentStore->id,
        ]);

        $usageLineItem = SubscriptionLineItem::factory()->create([
            'subscription_id' => $subscription->id,
        ]);
        $recurringLineItem = SubscriptionLineItem::factory()->recurringSubscription()->create([
            'subscription_id' => $subscription->id,
        ]);

        $usageConfig = $this->usageConfigService->createForSubscription(SubscriptionValue::from($subscription));

        $this->assertNull($usageConfig);
        $this->assertDatabaseCount('billings_app_usage_configs', 0);
    }

    public function testItDoesNotCreatesUsageConfigForSubscriptionNoSubscriptionItems(): void
    {
        /**
         * @var Subscription $subscription
         */
        $subscription = Subscription::factory()->withShopifyData()->active()->create([
            'store_id' => $this->currentStore->id,
        ]);

        $usageConfig = $this->usageConfigService->createForSubscription(SubscriptionValue::from($subscription));

        $this->assertNull($usageConfig);
        $this->assertDatabaseCount('billings_app_usage_configs', 0);
    }

    public function testItDoesNotCreatesUsageConfigForSubscriptionNoUsageSubscriptionItem(): void
    {
        /**
         * @var Subscription $subscription
         */
        $subscription = Subscription::factory()->withShopifyData()->active()->create([
            'store_id' => $this->currentStore->id,
        ]);

        $recurringLineItem = SubscriptionLineItem::factory()->recurringSubscription()->create([
            'subscription_id' => $subscription->id,
        ]);

        $usageConfig = $this->usageConfigService->createForSubscription(SubscriptionValue::from($subscription));

        $this->assertNull($usageConfig);
        $this->assertDatabaseCount('billings_app_usage_configs', 0);
    }

    public function testItCreatesUsageConfigForSubscription(): void
    {
        /**
         * @var Subscription $subscription
         */
        $subscription = Subscription::factory()->withShopifyData()->active()->create([
            'store_id' => $this->currentStore->id,
        ]);

        $usageLineItem = SubscriptionLineItem::factory()->create([
            'subscription_id' => $subscription->id,
        ]);
        $recurringLineItem = SubscriptionLineItem::factory()->recurringSubscription()->create([
            'subscription_id' => $subscription->id,
        ]);

        $usageConfig = $this->usageConfigService->createForSubscription(SubscriptionValue::from($subscription));

        $this->assertDatabaseCount('billings_app_usage_configs', 1);
        $this->assertEquals($usageLineItem->shopify_app_subscription_line_item_id, $usageConfig->subscriptionLineItemId);
    }
}
