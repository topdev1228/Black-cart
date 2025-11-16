<?php
declare(strict_types=1);

namespace Tests\Feature\Domain\Orders\Listeners;

use App\Domain\Orders\Listeners\SendReAuthSuccessEmailOnReAuthSuccessListener;
use App\Domain\Orders\Mail\ReAuthSuccess;
use App\Domain\Orders\Models\Order;
use App\Domain\Orders\Values\Order as OrderValue;
use App\Domain\Orders\Values\ReAuthSuccessEvent as ReAuthSuccessEventValue;
use App\Domain\Payments\Events\ReAuthSuccessEvent;
use App\Domain\Stores\Models\Store;
use App\Domain\Stores\Values\Store as StoreValue;
use Brick\Money\Money;
use Feature;
use Illuminate\Support\Facades\App;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Mail;
use Tests\TestCase;

class SendReAuthSuccessEmailOnReAuthSuccessListenerTest extends TestCase
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
        App::context()->store = StoreValue::from($this->store);
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

        $event = ReAuthSuccessEventValue::from(
            (new ReAuthSuccessEvent(Money::of(1500, 'USD'), $orderValue->sourceId))->broadcastWith(),
        );

        $listener = resolve(SendReAuthSuccessEmailOnReAuthSuccessListener::class);
        $listener->handle($event);

        Mail::assertSent(ReAuthSuccess::class);
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

        $event = ReAuthSuccessEventValue::from(
            (new ReAuthSuccessEvent(Money::of(100, 'USD'), $orderValue->sourceId))->broadcastWith(),
        );

        $listener = resolve(SendReAuthSuccessEmailOnReAuthSuccessListener::class);
        $listener->handle($event);

        Mail::assertNotSent(ReAuthSuccess::class);
    }

    public function testItDoesNotSendsEmailOnFeatureFlagOff(): void
    {
        Feature::fake(['shopify-perm-b-merchant-reauth-success-email']);

        Mail::fake();

        $order = Order::withoutEvents(function () {
            return Order::factory()->create([
                'store_id' => $this->store->id,
            ]);
        });
        $orderValue = OrderValue::from($order);

        $event = ReAuthSuccessEventValue::from(
            (new ReAuthSuccessEvent(Money::of(100, 'USD'), $orderValue->sourceId))->broadcastWith(),
        );

        $listener = resolve(SendReAuthSuccessEmailOnReAuthSuccessListener::class);
        $listener->handle($event);

        Mail::assertNotSent(ReAuthSuccess::class);
    }
}
