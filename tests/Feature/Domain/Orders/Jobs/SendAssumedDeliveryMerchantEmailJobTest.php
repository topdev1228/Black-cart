<?php

declare(strict_types=1);

namespace Tests\Feature\Domain\Orders\Jobs;

use App\Domain\Orders\Enums\LineItemStatus;
use App\Domain\Orders\Enums\OrderStatus;
use App\Domain\Orders\Jobs\SendAssumedDeliveryMerchantEmailJob;
use App\Domain\Orders\Mail\AssumedDeliveryMerchantReminder;
use App\Domain\Orders\Models\Order;
use App\Domain\Orders\Services\OrderService;
use App\Domain\Orders\Values\LineItem;
use App\Domain\Orders\Values\Order as OrderValue;
use App\Domain\Stores\Models\Store;
use App\Domain\Stores\Values\Store as StoreValue;
use Illuminate\Support\Facades\App;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Mail;
use Tests\TestCase;

class SendAssumedDeliveryMerchantEmailJobTest extends TestCase
{
    protected Store $store;
    protected OrderService $orderService;

    protected function setUp(): void
    {
        parent::setUp();

        $this->orderService = resolve(OrderService::class);
        $this->store = Store::withoutEvents(function () {
            return Store::factory()->create();
        });

        App::context(store: StoreValue::from($this->store));
        Mail::fake();
    }

    public function testItSendsEmail(): void
    {
        Http::fake([
            'http://localhost:8080/api/stores/settings' => Http::response(['settings' => ['customerSupportEmail' => ['value' => 'yuriyleve@gmail.com']]], 200),
        ]);

        $order = Order::factory()->create([
                'store_id' => $this->store->id,
            ]);
        $orderValue = OrderValue::from($order);

        $job = new SendAssumedDeliveryMerchantEmailJob($orderValue);
        $job->handle($this->orderService);

        Mail::assertSent(AssumedDeliveryMerchantReminder::class);
    }

    public function testItDoesNotFoundOrder(): void
    {
        $orderValue = OrderValue::builder()->create();
        $orderValue->id = 'non-existent-order-id';

        $job = new SendAssumedDeliveryMerchantEmailJob($orderValue);
        $result = $job->handle($this->orderService);

        $this->assertNull($result);

        Mail::assertNothingSent();
    }

    public function testIfOrderAlreadySentEmail(): void
    {
        $order = Order::factory()->create([
            'id' => $this->store->id,
            'status' => OrderStatus::OPEN,
            'assumed_delivery_merchant_email_sent_at' => now(),
        ]);
        $orderValue = OrderValue::from($order);
        $job = new SendAssumedDeliveryMerchantEmailJob($orderValue);
        $job->handle($this->orderService);

        Mail::assertNothingSent();
    }

    public function testIfOrderIsCancelled(): void
    {
        $order = Order::factory()->create([
            'id' => $this->store->id,
            'status' => OrderStatus::CANCELLED,
            'assumed_delivery_merchant_email_sent_at' => null,
        ]);
        $orderValue = OrderValue::from($order);
        $job = new SendAssumedDeliveryMerchantEmailJob($orderValue);
        $job->handle($this->orderService);

        Mail::assertNothingSent();
    }

    public function testIfMerchantEmailIsNull(): void
    {
        Http::fake([
            'http://localhost:8080/api/stores/settings' => Http::response(['settings' => ['customerSupportEmail' => ['value' => null]]], 200),
        ]);

        $order = Order::factory()->create();
        $orderValue = OrderValue::from($order);
        $job = new SendAssumedDeliveryMerchantEmailJob($orderValue);
        $job->handle($this->orderService);

        Mail::assertNothingSent();
    }

    public function testIfOrderIsInTrial(): void
    {
        $order = Order::factory()->create([
            'id' => $this->store->id,
            'status' => OrderStatus::IN_TRIAL,
            'assumed_delivery_merchant_email_sent_at' => null,
        ]);
        $orderValue = OrderValue::from($order);
        $job = new SendAssumedDeliveryMerchantEmailJob($orderValue);
        $job->handle($this->orderService);

        Mail::assertNothingSent();
    }

    public function testIfOrderHasNonOpenLineItems(): void
    {
        $order = Order::factory()->create([
            'id' => $this->store->id,
            'status' => OrderStatus::OPEN,
            'assumed_delivery_merchant_email_sent_at' => null,
        ]);
        $orderValue = OrderValue::from($order);
        $orderValue->lineItems = LineItem::collection([
            LineItem::builder()->create([
                'order_id' => $order->id,
                'status' => LineItemStatus::DELIVERED,
            ]),
        ]);
        $job = new SendAssumedDeliveryMerchantEmailJob($orderValue);
        $job->handle($this->orderService);

        Mail::assertNothingSent();
    }
}
