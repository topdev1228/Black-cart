<?php
declare(strict_types=1);

namespace Tests\Feature\Domain\Orders\Listeners;

use App\Domain\Orders\Enums\OrderStatus;
use App\Domain\Orders\Events\PaymentRequiredEvent;
use App\Domain\Orders\Listeners\WebhookPaymentSchedulesDueListener;
use App\Domain\Orders\Models\Order;
use App\Domain\Orders\Models\TrialGroup;
use App\Domain\Orders\Values\WebhookPaymentSchedulesDue;
use App\Domain\Stores\Models\Store;
use App\Domain\Stores\Values\Store as StoreValue;
use Carbon\CarbonImmutable;
use Event;
use Illuminate\Support\Facades\App;
use PHPUnit\Framework\Attributes\DataProvider;
use Tests\TestCase;

class WebhookPaymentSchedulesDueListenerTest extends TestCase
{
    protected $store;

    protected function setUp(): void
    {
        parent::setUp();

        $this->store = StoreValue::from(Store::withoutEvents(function () {
            return Store::factory()->create();
        }));
        App::context(store: $this->store);
    }

    public function testItMarshalsToWebhookPaymentSchedulesDueValue(): void
    {
        $webhook = collect($this->loadFixtureData('paymentSchedulesDueWebhook.json', 'Orders'));
        $webhookPaymentSchedulesDueValue = WebhookPaymentSchedulesDue::from($webhook);

        $this->assertEquals($webhook['admin_graphql_api_id'], $webhookPaymentSchedulesDueValue->paymentScheduleSourceId);
        $this->assertEquals($webhook['payment_terms_id'], $webhookPaymentSchedulesDueValue->paymentTermsId);
        $this->assertEquals($webhook['currency'], $webhookPaymentSchedulesDueValue->customerCurrency->value);
        $this->assertEquals(floatval($webhook['amount']), $webhookPaymentSchedulesDueValue->customerAmount->getAmount()->toFloat());
        $this->assertEquals(CarbonImmutable::parse($webhook['issued_at']), $webhookPaymentSchedulesDueValue->issuedAt);
        $this->assertEquals(CarbonImmutable::parse($webhook['due_at']), $webhookPaymentSchedulesDueValue->dueAt);
        $this->assertEquals(CarbonImmutable::parse($webhook['completed_at']), $webhookPaymentSchedulesDueValue->completedAt);
    }

    public function testItDoesNotExpireTrialFromShopifyPaymentScheduleOnOrderNotFound(): void
    {
        Event::fake([
            PaymentRequiredEvent::class,
        ]);

        $webhook = collect($this->loadFixtureData('paymentSchedulesDueWebhook.json', 'Orders'));
        $webhookPaymentSchedulesDueValue = WebhookPaymentSchedulesDue::from($webhook);

        $listener = resolve(WebhookPaymentSchedulesDueListener::class);
        $listener->handle($webhookPaymentSchedulesDueValue);

        Event::assertNotDispatched(PaymentRequiredEvent::class);
    }

    #[DataProvider('expireTrialFromShopifyPaymentScheduleOrderStatusesNotInTrial')]
    public function testItDoesNotExpireTrialFromShopifyPaymentScheduleOnOrderNotInTrial(OrderStatus $status): void
    {
        Event::fake([
            PaymentRequiredEvent::class,
        ]);

        $webhook = collect($this->loadFixtureData('paymentSchedulesDueWebhook.json', 'Orders'));
        $webhookPaymentSchedulesDueValue = WebhookPaymentSchedulesDue::from($webhook);

        Order::withoutEvents(function () use ($status, $webhookPaymentSchedulesDueValue) {
            return Order::factory()->create([
                'store_id' => $this->store->id,
                'status' => $status,
                'payment_terms_id' => $webhookPaymentSchedulesDueValue->paymentTermsId,
            ]);
        });

        $listener = resolve(WebhookPaymentSchedulesDueListener::class);
        $listener->handle($webhookPaymentSchedulesDueValue);

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

        $webhook = collect($this->loadFixtureData('paymentSchedulesDueWebhook.json', 'Orders'));
        $webhookPaymentSchedulesDueValue = WebhookPaymentSchedulesDue::from($webhook);

        $order = Order::withoutEvents(function () use ($webhookPaymentSchedulesDueValue) {
            return Order::factory()->create([
                'store_id' => $this->store->id,
                'status' => OrderStatus::IN_TRIAL,
                'payment_terms_id' => $webhookPaymentSchedulesDueValue->paymentTermsId,
            ]);
        });

        $listener = resolve(WebhookPaymentSchedulesDueListener::class);
        $listener->handle($webhookPaymentSchedulesDueValue);

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

        $webhook = collect($this->loadFixtureData('paymentSchedulesDueWebhook.json', 'Orders'));
        $webhookPaymentSchedulesDueValue = WebhookPaymentSchedulesDue::from($webhook);

        $order = Order::withoutEvents(function () use ($webhookPaymentSchedulesDueValue) {
            return Order::factory()->create([
                'store_id' => $this->store->id,
                'status' => OrderStatus::IN_TRIAL,
                'payment_terms_id' => $webhookPaymentSchedulesDueValue->paymentTermsId,
            ]);
        });

        $trialGroup = TrialGroup::withoutEvents(function () use ($order) {
            return TrialGroup::factory()->create([
                'order_id' => $order->id,
            ]);
        });

        $listener = resolve(WebhookPaymentSchedulesDueListener::class);
        $listener->handle($webhookPaymentSchedulesDueValue);

        Event::assertDispatched(PaymentRequiredEvent::class, function (PaymentRequiredEvent $event) use ($order, $trialGroup) {
            $this->assertEquals($order->id, $event->orderId);
            $this->assertEquals($order->source_id, $event->sourceOrderId);
            $this->assertEquals($trialGroup->id, $event->trialGroupId);
            $this->assertEquals($order->outstanding_customer_amount, $event->amount);

            return true;
        });
    }
}
