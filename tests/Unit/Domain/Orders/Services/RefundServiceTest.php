<?php

declare(strict_types=1);

namespace Tests\Unit\Domain\Orders\Services;

use App;
use App\Domain\Orders\Enums\LineItemStatus;
use App\Domain\Orders\Events\RefundCreatedEvent;
use App\Domain\Orders\Models\LineItem;
use App\Domain\Orders\Models\Order;
use App\Domain\Orders\Repositories\RefundRepository;
use App\Domain\Orders\Services\RefundService;
use App\Domain\Orders\Values\WebhookRefundsCreate;
use App\Domain\Orders\Values\WebhookRefundsCreateRefundLineItem;
use App\Domain\Stores\Models\Store;
use Event;
use Illuminate\Support\Facades\Date;
use Illuminate\Support\Str;
use Mockery\MockInterface;
use PHPUnit\Framework\Attributes\DataProvider;
use Ramsey\Uuid\Uuid;
use Tests\Fixtures\Domains\Orders\RefundData;
use Tests\TestCase;

class RefundServiceTest extends TestCase
{
    use RefundData;

    #[DataProvider('refundDataProvider')]
    #[DataProvider('generatedDataProvider')]
    #[DataProvider('realWebhookDataProvider')]
    public function testItCreatesRefund(
        string $storeId,
        string $orderId,
        string $shopCurrency,
        string $customerCurrency,
        array $lineItems,
        array $webhookData,
        array $refundLineItems,
        array $refund,
    ): void {
        Str::createUuidsUsingSequence([$refund['id']], fn () => Uuid::uuid4()->toString());

        Event::fake(RefundCreatedEvent::class);

        $store = Store::withoutEvents(function () use ($storeId, $lineItems) {
            return Store::factory()->create(['id' => $storeId, 'currency' => $lineItems[0]['shop_currency']]);
        });
        App::context(store: $store);

        $order = Order::withoutEvents(function () use ($orderId, $store, $shopCurrency, $customerCurrency) {
            return Order::factory()->create([
                'id' => $orderId,
                'store_id' => $store->id,
                'source_id' => Str::shopifyGid('12345', 'Order'),
                'shop_currency' => $shopCurrency,
                'customer_currency' => $customerCurrency,
            ]);
        });

        $refundService = resolve(RefundService::class);

        foreach ($lineItems as $lineItem) {
            $lineItem['source_id'] = Str::shopifyGid($lineItem['source_id'], 'LineItem');
            LineItem::withoutEvents(function () use ($order, $lineItem) {
                LineItem::factory()->state(['order_id' => $order->id])->create($lineItem);
            });
        }

        $webhookRefundsCreate = WebhookRefundsCreate::builder()->create($webhookData);
        $webhookRefundsCreate->sourceOrderId = $order->source_id;

        $refundService->createFromWebhook($webhookRefundsCreate);

        $hasTransaction = (bool) $webhookRefundsCreate->transactions?->count();
        $this->assertDatabaseCount('orders_refund_line_items', count($refundLineItems));
        foreach ($refundLineItems as $refundLineItem) {
            $refundLineItem['source_refund_reference_id'] = $webhookRefundsCreate->sourceId;
            $refundLineItem['line_item_id'] = Str::shopifyGid($refundLineItem['line_item_id'], 'LineItem');
            $this->assertDatabaseHas('orders_refund_line_items', $refundLineItem);
        }

        $refund['source_refund_reference_id'] = Str::shopifyGid($refund['source_refund_reference_id'], 'Refund');
        $refund['order_level_refund_customer_amount'] = $refund['order_level_refund_customer_amount']->getMinorAmount()->toInt();
        $refund['order_level_refund_shop_amount'] = $refund['order_level_refund_shop_amount']->getMinorAmount()->toInt();
        $refund['tbyb_total_customer_amount'] = $refund['tbyb_total_customer_amount']->getMinorAmount()->toInt();
        $refund['tbyb_total_shop_amount'] = $refund['tbyb_total_shop_amount']->getMinorAmount()->toInt();
        $refund['upfront_total_customer_amount'] = $refund['upfront_total_customer_amount']->getMinorAmount()->toInt();
        $refund['upfront_total_shop_amount'] = $refund['upfront_total_shop_amount']->getMinorAmount()->toInt();
        $refund['refund_data'] = $webhookRefundsCreate->toJson();

        if ($hasTransaction) {
            $refund['refunded_customer_amount'] = $refund['refunded_customer_amount']?->getMinorAmount()->toInt() ?? 0;
            $refund['refunded_shop_amount'] = $refund['refunded_shop_amount']?->getMinorAmount()->toInt() ?? 0;
        } else {
            $refund['refunded_customer_amount'] = 0;
            $refund['refunded_shop_amount'] = 0;
        }

        $this->assertDatabaseCount('orders_refunds', 1);
        $this->assertDatabaseHas('orders_refunds', $refund);
    }

    public function testItIgnoresInternalRefunds(): void
    {
        Event::fake(RefundCreatedEvent::class);

        $store = Store::withoutEvents(function () {
            return Store::factory()->create(['id' => 'test-store-id', 'currency' => 'USD']);
        });
        App::context(store: $store);

        $order = Order::withoutEvents(function () use ($store) {
            return Order::factory()->create([
                'id' => 'test-order-id',
                'store_id' => $store->id,
                'source_id' => Str::shopifyGid('12345', 'Order'),
                'shop_currency' => 'USD',
                'customer_currency' => 'USD',
            ]);
        });

        $refundService = resolve(RefundService::class);

        LineItem::withoutEvents(function () use ($order) {
            LineItem::factory()->state(['order_id' => $order->id, 'status' => LineItemStatus::INTERNAL, 'source_id' => 'gid://shopify/LineItem/10956880806017'])->create();
        });

        $webhookRefundsCreate = WebhookRefundsCreate::builder()->create([
            'refund_line_items' => [
                WebhookRefundsCreateRefundLineItem::builder()->create([
                    'id' => 10956880806017,
                    'quantity' => 1,
                    'line_item_id' => 'gid://shopify/LineItem/10956880806017',
                ]),
            ],
        ]);
        $webhookRefundsCreate->sourceOrderId = $order->source_id;

        $refundService->createFromWebhook($webhookRefundsCreate);

        $this->assertDatabaseCount('orders_refund_line_items', 0);
        $this->assertDatabaseCount('orders_refunds', 0);
    }

    public function testItGetsGrossSales(): void
    {
        $date = Date::now();
        $grossSales = 15000;

        $this->mock(RefundRepository::class, function (MockInterface $mock) use ($date, $grossSales) {
            $mock->expects('getGrossSales')->withArgs([$date, null])->andReturn($grossSales);
        });

        $refundService = resolve(RefundService::class);
        $return = $refundService->getGrossSales($date);

        $this->assertEquals($grossSales, $return);
    }

    public function testItGetsDiscounts(): void
    {
        $date = Date::now();
        $discounts = 15000;

        $this->mock(RefundRepository::class, function (MockInterface $mock) use ($date, $discounts) {
            $mock->expects('getDiscounts')->withArgs([$date, null])->andReturn($discounts);
        });

        $refundService = resolve(RefundService::class);
        $return = $refundService->getDiscounts($date);

        $this->assertEquals($discounts, $return);
    }
}
