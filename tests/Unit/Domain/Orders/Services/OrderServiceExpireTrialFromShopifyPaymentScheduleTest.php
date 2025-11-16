<?php
declare(strict_types=1);

namespace Tests\Unit\Domain\Orders\Services;

use App;
use App\Domain\Orders\Enums\OrderStatus;
use App\Domain\Orders\Events\PaymentRequiredEvent;
use App\Domain\Orders\Models\Order;
use App\Domain\Orders\Models\TrialGroup;
use App\Domain\Orders\Repositories\TrialGroupRepository;
use App\Domain\Orders\Services\OrderService;
use App\Domain\Orders\Values\WebhookPaymentSchedulesDue;
use App\Domain\Stores\Models\Store;
use Event;
use PHPUnit\Framework\Attributes\DataProvider;
use Tests\TestCase;

class OrderServiceExpireTrialFromShopifyPaymentScheduleTest extends TestCase
{
    protected Store $store;

    protected function setUp(): void
    {
        parent::setUp();

        $this->store = Store::withoutEvents(function () {
            return Store::factory()->create();
        });
        App::context(store: $this->store);
    }

    public function testItDoesNotExpireTrialFromShopifyPaymentScheduleOnOrderNotFound(): void
    {
        Event::fake([
            PaymentRequiredEvent::class,
        ]);

        $paymentScheduleDue = WebhookPaymentSchedulesDue::builder()->create();

        $this->mock(TrialGroupRepository::class)->shouldNotReceive('getByOrder');

        $orderSservice = resolve(OrderService::class);
        $orderSservice->expireTrialFromShopifyPaymentSchedule($paymentScheduleDue);

        Event::assertNotDispatched(PaymentRequiredEvent::class);
    }

    #[DataProvider('expireTrialFromShopifyPaymentScheduleOrderStatusesNotInTrial')]
    public function testItDoesNotExpireTrialFromShopifyPaymentScheduleOnOrderNotInTrial(OrderStatus $status): void
    {
        Event::fake([
            PaymentRequiredEvent::class,
        ]);

        $paymentScheduleDue = WebhookPaymentSchedulesDue::builder()->create();
        Order::withoutEvents(function () use ($status, $paymentScheduleDue) {
            return Order::factory()->create([
                'store_id' => $this->store->id,
                'status' => $status,
                'payment_terms_id' => $paymentScheduleDue->paymentTermsId,
            ]);
        });

        $this->mock(TrialGroupRepository::class)->shouldNotReceive('getByOrder');

        $orderSservice = resolve(OrderService::class);
        $orderSservice->expireTrialFromShopifyPaymentSchedule($paymentScheduleDue);

        Event::assertNotDispatched(PaymentRequiredEvent::class);
    }

    public static function expireTrialFromShopifyPaymentScheduleOrderStatusesNotInTrial(): array
    {
        return [
            [OrderStatus::OPEN],
            [OrderStatus::ARCHIVED],
            [OrderStatus::CANCELLED],
            [OrderStatus::PAYMENT_PENDING],
            [OrderStatus::PAYMENT_AUTHORIZED],
            [OrderStatus::PAYMENT_OVERDUE],
            [OrderStatus::PAYMENT_EXPIRING],
            [OrderStatus::PAYMENT_EXPIRED],
            [OrderStatus::PAYMENT_PAID],
            [OrderStatus::PAYMENT_REFUNDED],
            [OrderStatus::PAYMENT_PARTIALLY_REFUNDED],
            [OrderStatus::PAYMENT_PARTIALLY_PAID],
            [OrderStatus::PAYMENT_VOIDED],
            [OrderStatus::PAYMENT_UNPAID],
            [OrderStatus::FULFILLMENT_FULFILLED],
            [OrderStatus::FULFILLMENT_UNFULFILLED],
            [OrderStatus::FULFILLMENT_PARTIALLY_FULFILLED],
            [OrderStatus::FULFILLMENT_SCHEDULED],
            [OrderStatus::FULFILLMENT_ON_HOLD],
            [OrderStatus::RETURN_REQUESTED],
            [OrderStatus::RETURN_IN_PROGRESS],
            [OrderStatus::RETURN_FAILED],
            [OrderStatus::RETURN_INSPECTION_COMPLETE],
            [OrderStatus::RETURN_COMPLETED],
            [OrderStatus::COMPLETED],
        ];
    }

    public function testItExpiresTrialFromShopifyPaymentScheduleOnOrderTrialGroupNotFound(): void
    {
        Event::fake([
            PaymentRequiredEvent::class,
        ]);

        $paymentScheduleDue = WebhookPaymentSchedulesDue::builder()->create();
        $order = Order::withoutEvents(function () use ($paymentScheduleDue) {
            return Order::factory()->create([
                'store_id' => $this->store->id,
                'status' => OrderStatus::IN_TRIAL,
                'payment_terms_id' => $paymentScheduleDue->paymentTermsId,
            ]);
        });

        $orderSservice = resolve(OrderService::class);
        $orderSservice->expireTrialFromShopifyPaymentSchedule($paymentScheduleDue);

        Event::assertDispatched(PaymentRequiredEvent::class, function (PaymentRequiredEvent $event) use ($order) {
            $this->assertEquals($order->id, $event->orderId);
            $this->assertEquals($order->source_id, $event->sourceOrderId);
            $this->assertEquals($order->id, $event->trialGroupId);
            $this->assertEquals($order->outstanding_customer_amount, $event->amount);

            return true;
        });
    }

    public function testItExpiresTrialFromShopifyPaymentSchedule(): void
    {
        Event::fake([
            PaymentRequiredEvent::class,
        ]);

        $paymentScheduleDue = WebhookPaymentSchedulesDue::builder()->create();
        $order = Order::withoutEvents(function () use ($paymentScheduleDue) {
            return Order::factory()->create([
                'store_id' => $this->store->id,
                'status' => OrderStatus::IN_TRIAL,
                'payment_terms_id' => $paymentScheduleDue->paymentTermsId,
            ]);
        });

        $trialGroup = TrialGroup::withoutEvents(function () use ($order) {
            return TrialGroup::factory()->create([
                'order_id' => $order->id,
            ]);
        });

        $orderSservice = resolve(OrderService::class);
        $orderSservice->expireTrialFromShopifyPaymentSchedule($paymentScheduleDue);

        Event::assertDispatched(PaymentRequiredEvent::class, function (PaymentRequiredEvent $event) use ($order, $trialGroup) {
            $this->assertEquals($order->id, $event->orderId);
            $this->assertEquals($order->source_id, $event->sourceOrderId);
            $this->assertEquals($trialGroup->id, $event->trialGroupId);
            $this->assertEquals($order->outstanding_customer_amount, $event->amount);

            return true;
        });
    }
}
