<?php
declare(strict_types=1);

namespace Tests\Feature\Domain\Orders\Listeners;

use App\Domain\Orders\Listeners\SendReAuthFailedEmailOnReAuthFailedListener;
use App\Domain\Orders\Mail\ReAuthFailed;
use App\Domain\Orders\Models\Order;
use App\Domain\Orders\Values\Order as OrderValue;
use App\Domain\Orders\Values\ReAuthFailedEvent as ReAuthFailedEventValue;
use App\Domain\Payments\Events\ReAuthFailedEvent;
use App\Domain\Stores\Models\Store;
use Brick\Money\Money;
use Carbon\CarbonImmutable;
use Feature;
use Illuminate\Support\Facades\App;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Mail;
use Tests\TestCase;

class SendReAuthFailedEmailOnReAuthFailedListenerTest extends TestCase
{
    protected Store $store;

    protected function setUp(): void
    {
        parent::setUp();

        Http::fake([
            'http://localhost:8080/api/stores/settings' => Http::response([
                'settings' => [
                    'customerSupportEmail' => [
                        'value' => 'customersupport@merchant.com',
                    ],
                ],
            ], 200),
        ]);

        $this->store = Store::withoutEvents(function () {
            return Store::factory()->create();
        });
        App::context(store: $this->store);
    }

    public function testItSendsEmail(): void
    {
        Mail::fake();

        $order = Order::withoutEvents(function () {
            return Order::factory()->create([
                'store_id' => $this->store->id,
                'order_data' => [
                    'email' => 'matthew+test@blackcart.com',
                    'customer' => [
                        'first_name' => 'Matthew',
                    ],
                    'name' => '#1001',
                ],
            ]);
        });
        $orderValue = OrderValue::from($order);

        $event = ReAuthFailedEventValue::from(
            (new ReAuthFailedEvent(CarbonImmutable::now(), Money::of(10000, 'USD'), $orderValue->sourceId))->broadcastWith(),
        );

        $listener = resolve(SendReAuthFailedEmailOnReAuthFailedListener::class);
        $listener->handle($event);

        Mail::assertSent(ReAuthFailed::class);
    }

    public function testItDoesNotSendsEmailOnKillSwitchEnabled(): void
    {
        Feature::fake(['shopify-perm-b-kill-reauth']);

        Mail::fake();

        $order = Order::withoutEvents(function () {
            return Order::factory()->create([
                'store_id' => $this->store->id,
            ]);
        });
        $orderValue = OrderValue::from($order);

        $event = ReAuthFailedEventValue::from(
            (new ReAuthFailedEvent(CarbonImmutable::now(), Money::of(100, 'USD'), $orderValue->sourceId))->broadcastWith(),
        );

        $listener = resolve(SendReAuthFailedEmailOnReAuthFailedListener::class);
        $listener->handle($event);

        Mail::assertNotSent(ReAuthFailed::class);
    }

    public function testItDoesNotSendsEmailOnEmailFeatureFlagOff(): void
    {
        Feature::fake(['shopify-perm-b-merchant-reauth-failed-email']);

        Mail::fake();

        $order = Order::withoutEvents(function () {
            return Order::factory()->create([
                'store_id' => $this->store->id,
            ]);
        });
        $orderValue = OrderValue::from($order);

        $event = ReAuthFailedEventValue::from(
            (new ReAuthFailedEvent(CarbonImmutable::now(), Money::of(100, 'USD'), $orderValue->sourceId))->broadcastWith(),
        );

        $listener = resolve(SendReAuthFailedEmailOnReAuthFailedListener::class);
        $listener->handle($event);

        Mail::assertNotSent(ReAuthFailed::class);
    }
}
