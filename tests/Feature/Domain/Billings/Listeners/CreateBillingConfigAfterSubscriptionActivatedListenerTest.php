<?php
declare(strict_types=1);

namespace Feature\Domain\Billings\Listeners;

use App;
use App\Domain\Billings\Listeners\CreateBillingConfigAfterSubscriptionActivatedListener;
use App\Domain\Billings\Models\Subscription;
use App\Domain\Billings\Models\SubscriptionLineItem;
use App\Domain\Billings\Repositories\SubscriptionRepository;
use App\Domain\Billings\Services\UsageConfigService;
use App\Domain\Billings\Values\Subscription as SubscriptionValue;
use App\Domain\Billings\Values\SubscriptionActivatedEvent;
use App\Domain\Stores\Models\Store;
use Tests\TestCase;

class CreateBillingConfigAfterSubscriptionActivatedListenerTest extends TestCase
{
    protected Store $currentStore;
    protected UsageConfigService $service;
    protected SubscriptionRepository $repository;

    protected function setUp(): void
    {
        parent::setUp();
        $this->currentStore = Store::withoutEvents(function () {
            return Store::factory()->create();
        });
        App::context(store: $this->currentStore);
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

        $event = new SubscriptionActivatedEvent(subscription: SubscriptionValue::from($subscription));
        $listener = resolve(CreateBillingConfigAfterSubscriptionActivatedListener::class);
        $usageConfig = $listener->handle($event);

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

        $event = new SubscriptionActivatedEvent(subscription: SubscriptionValue::from($subscription));
        $listener = resolve(CreateBillingConfigAfterSubscriptionActivatedListener::class);
        $usageConfig = $listener->handle($event);

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

        $event = new SubscriptionActivatedEvent(subscription: SubscriptionValue::from($subscription));
        $listener = resolve(CreateBillingConfigAfterSubscriptionActivatedListener::class);
        $usageConfig = $listener->handle($event);

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

        $event = new SubscriptionActivatedEvent(subscription: SubscriptionValue::from($subscription));
        $listener = resolve(CreateBillingConfigAfterSubscriptionActivatedListener::class);
        $usageConfig = $listener->handle($event);

        $this->assertDatabaseCount('billings_app_usage_configs', 1);
        $this->assertEquals($usageLineItem->shopify_app_subscription_line_item_id, $usageConfig->subscriptionLineItemId);
    }
}
