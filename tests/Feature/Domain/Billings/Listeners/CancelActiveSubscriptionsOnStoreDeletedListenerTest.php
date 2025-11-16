<?php
declare(strict_types=1);

namespace Tests\Feature\Domain\Billings\Listeners;

use App;
use App\Domain\Billings\Enums\SubscriptionStatus;
use App\Domain\Billings\Events\SubscriptionDeactivatedEvent;
use App\Domain\Billings\Listeners\CancelActiveSubscriptionsOnStoreDeletedListener;
use App\Domain\Billings\Models\Subscription;
use App\Domain\Billings\Values\Store as BillingsStoreValue;
use App\Domain\Billings\Values\StoreDeletedEvent as StoreDeletedEventValue;
use App\Domain\Stores\Models\Store;
use App\Domain\Stores\Values\Store as StoreValue;
use Event;
use Illuminate\Support\Facades\Date;
use Tests\Fixtures\Domains\Billings\Traits\WebhookShopifyAppSubscriptionsUpdateTestData;
use Tests\TestCase;

class CancelActiveSubscriptionsOnStoreDeletedListenerTest extends TestCase
{
    use WebhookShopifyAppSubscriptionsUpdateTestData;

    protected Store $store;

    protected function setUp(): void
    {
        parent::setUp();

        $this->currentStore = Store::withoutEvents(function () {
            return Store::factory()->create();
        });
        App::context(store: StoreValue::from($this->currentStore));

        Date::setTestNow('2022-02-20 00:00:00');
    }

    public function testItCancelsOneActiveSubscription(): void
    {
        Event::fake([
            SubscriptionDeactivatedEvent::class,
        ]);

        $declinedSubscription = Subscription::withoutEvents(function () {
            return Subscription::factory()->declined()->withShopifyData()->create([
                'store_id' => $this->currentStore->id,
            ]);
        });
        $activeSubscription = Subscription::withoutEvents(function () {
            return Subscription::factory()->active()->withShopifyData()->create([
                'store_id' => $this->currentStore->id,
            ]);
        });

        $storeDeletedEventvalue = new StoreDeletedEventValue(
            store: BillingsStoreValue::from($this->currentStore->toArray())
        );

        $listener = resolve(CancelActiveSubscriptionsOnStoreDeletedListener::class);
        $listener->handle($storeDeletedEventvalue);

        $activeSubscription->refresh();
        $this->assertEquals(SubscriptionStatus::CANCELLED, $activeSubscription->status);
        $this->assertEquals(Date::now(), $activeSubscription->deactivated_at);
        $this->assertEquals(Date::now(), $activeSubscription->deleted_at);

        // Ensure that the declined subscription is not affected
        $declinedSubscription->refresh();
        $this->assertEquals(SubscriptionStatus::DECLINED, $declinedSubscription->status);

        Event::assertDispatchedTimes(SubscriptionDeactivatedEvent::class, 1);
        Event::assertDispatched(
            SubscriptionDeactivatedEvent::class,
            function (SubscriptionDeactivatedEvent $event) use ($activeSubscription) {
                $this->assertEquals($activeSubscription->id, $event->subscription->id);
                $this->assertEquals($activeSubscription->status, $event->subscription->status);

                return true;
            },
        );
    }

    public function testItCancelsMultipleActiveSubscriptions(): void
    {
        Event::fake([
            SubscriptionDeactivatedEvent::class,
        ]);

        $declinedSubscription = Subscription::withoutEvents(function () {
            return Subscription::factory()->declined()->withShopifyData()->create([
                'store_id' => $this->currentStore->id,
            ]);
        });
        $activeSubscriptions = Subscription::withoutEvents(function () {
            return Subscription::factory()->active()->count(3)->withShopifyData()->create([
                'store_id' => $this->currentStore->id,
            ]);
        });
        $activeSubscriptionIds = $activeSubscriptions->pluck('id')->toArray();

        $storeDeletedEventvalue = new StoreDeletedEventValue(
            store: BillingsStoreValue::from($this->currentStore->toArray())
        );

        $listener = resolve(CancelActiveSubscriptionsOnStoreDeletedListener::class);
        $listener->handle($storeDeletedEventvalue);

        foreach ($activeSubscriptions as $activeSubscription) {
            $activeSubscription->refresh();
            $this->assertEquals(SubscriptionStatus::CANCELLED, $activeSubscription->status);
            $this->assertEquals(Date::now(), $activeSubscription->deactivated_at);
            $this->assertEquals(Date::now(), $activeSubscription->deleted_at);
        }

        // Ensure that the declined subscription is not affected
        $declinedSubscription->refresh();
        $this->assertEquals(SubscriptionStatus::DECLINED, $declinedSubscription->status);

        Event::assertDispatchedTimes(SubscriptionDeactivatedEvent::class, 3);
    }
}
