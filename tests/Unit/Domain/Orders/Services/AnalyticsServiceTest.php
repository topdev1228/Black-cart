<?php
declare(strict_types=1);

namespace Tests\Unit\Domain\Orders\Services;

use App;
use App\Domain\Orders\Enums\OrderStatus;
use App\Domain\Orders\Enums\OrderSummaryStatus;
use App\Domain\Orders\Models\Order;
use App\Domain\Orders\Models\OrderReturn;
use App\Domain\Orders\Models\Refund;
use App\Domain\Orders\Repositories\OrderRepository;
use App\Domain\Orders\Services\AnalyticsService;
use App\Domain\Orders\Values\AnalyticsData;
use App\Domain\Stores\Models\Store;
use Arr;
use Illuminate\Support\Facades\Date;
use Str;
use Carbon\CarbonImmutable;
use Illuminate\Database\Eloquent\Factories\Sequence;
use Tests\TestCase;

class AnalyticsServiceTest extends TestCase
{
    protected AnalyticsService $service;
    protected OrderRepository $repositoryMock;
    protected Store $store;

    protected function setUp(): void
    {
        parent::setUp();

        CarbonImmutable::setTestNow(CarbonImmutable::create(2024, 4, 26));

        $this->store = Store::withoutEvents(function () {
            return Store::factory()->create([
                'created_at' => CarbonImmutable::create(2024, 1, 1),
            ]);
        });
        App::context(store: $this->store);

        $this->service = resolve(AnalyticsService::class);
    }

    public function testItGetsAnalyticsDataNoReturns(): void
    {
        Order::withoutEvents(function () {
            return Order::factory()->count(10)->state(new Sequence(
                [
                    'status' => OrderStatus::OPEN,
                    'created_at' => CarbonImmutable::create(2024, 4, 1, 0, 0, 0),
                    'total_net_sales_shop_amount' => 10000,
                    'original_total_gross_sales_shop_amount' => 10000,
                    'tbyb_net_sales_shop_amount' => 10000,
                    'original_total_discounts_shop_amount' => 0,
                    'tbyb_refund_gross_sales_shop_amount' => 0,
                    'upfront_refund_gross_sales_shop_amount' => 0,
                    'tbyb_refund_discounts_shop_amount' => 0,
                    'upfront_refund_discounts_shop_amount' => 0,
                ],
                [
                    'status' => OrderStatus::PAYMENT_PARTIALLY_PAID,
                    'created_at' => CarbonImmutable::create(2024, 4, 2, 0, 0, 0),
                    'total_net_sales_shop_amount' => 10000,
                    'original_total_gross_sales_shop_amount' => 10000,
                    'tbyb_net_sales_shop_amount' => 10000,
                    'original_total_discounts_shop_amount' => 0,
                    'tbyb_refund_gross_sales_shop_amount' => 0,
                    'upfront_refund_gross_sales_shop_amount' => 0,
                    'tbyb_refund_discounts_shop_amount' => 0,
                    'upfront_refund_discounts_shop_amount' => 0,
                ],
                [
                    'status' => OrderStatus::PAYMENT_AUTHORIZED,
                    'created_at' => CarbonImmutable::create(2024, 4, 3, 0, 0, 0),
                    'total_net_sales_shop_amount' => 10000,
                    'original_total_gross_sales_shop_amount' => 10000,
                    'tbyb_net_sales_shop_amount' => 10000,
                    'original_total_discounts_shop_amount' => 0,
                    'tbyb_refund_gross_sales_shop_amount' => 0,
                    'upfront_refund_gross_sales_shop_amount' => 0,
                    'tbyb_refund_discounts_shop_amount' => 0,
                    'upfront_refund_discounts_shop_amount' => 0,
                ],
                [
                    'status' => OrderStatus::PAYMENT_UNPAID,
                    'created_at' => CarbonImmutable::create(2024, 4, 4, 0, 0, 0),
                    'total_net_sales_shop_amount' => 10000,
                    'original_total_gross_sales_shop_amount' => 10000,
                    'tbyb_net_sales_shop_amount' => 10000,
                    'original_total_discounts_shop_amount' => 0,
                    'tbyb_refund_gross_sales_shop_amount' => 0,
                    'upfront_refund_gross_sales_shop_amount' => 0,
                    'tbyb_refund_discounts_shop_amount' => 0,
                    'upfront_refund_discounts_shop_amount' => 0,
                ],
                [
                    'status' => OrderStatus::FULFILLMENT_FULFILLED,
                    'created_at' => CarbonImmutable::create(2024, 4, 5, 0, 0, 0),
                    'total_net_sales_shop_amount' => 20000,
                    'original_total_gross_sales_shop_amount' => 20000,
                    'tbyb_net_sales_shop_amount' => 20000,
                    'original_total_discounts_shop_amount' => 0,
                    'tbyb_refund_gross_sales_shop_amount' => 0,
                    'upfront_refund_gross_sales_shop_amount' => 0,
                    'tbyb_refund_discounts_shop_amount' => 0,
                    'upfront_refund_discounts_shop_amount' => 0,
                ],
                [
                    'status' => OrderStatus::FULFILLMENT_PARTIALLY_FULFILLED,
                    'created_at' => CarbonImmutable::create(2024, 4, 6, 0, 0, 0),
                    'total_net_sales_shop_amount' => 20000,
                    'original_total_gross_sales_shop_amount' => 20000,
                    'tbyb_net_sales_shop_amount' => 20000,
                    'original_total_discounts_shop_amount' => 0,
                    'tbyb_refund_gross_sales_shop_amount' => 0,
                    'upfront_refund_gross_sales_shop_amount' => 0,
                    'tbyb_refund_discounts_shop_amount' => 0,
                    'upfront_refund_discounts_shop_amount' => 0,
                ],
                [
                    'status' => OrderStatus::IN_TRIAL,
                    'created_at' => CarbonImmutable::create(2024, 4, 7, 0, 0, 0),
                    'total_net_sales_shop_amount' => 30000,
                    'original_total_gross_sales_shop_amount' => 30000,
                    'tbyb_net_sales_shop_amount' => 30000,
                    'original_total_discounts_shop_amount' => 0,
                    'tbyb_refund_gross_sales_shop_amount' => 0,
                    'upfront_refund_gross_sales_shop_amount' => 0,
                    'tbyb_refund_discounts_shop_amount' => 0,
                    'upfront_refund_discounts_shop_amount' => 0,
                ],
                [
                    'status' => OrderStatus::PAYMENT_PAID,
                    'created_at' => CarbonImmutable::create(2024, 4, 8, 0, 0, 0),
                    'total_net_sales_shop_amount' => 40000,
                    'original_total_gross_sales_shop_amount' => 40000,
                    'tbyb_net_sales_shop_amount' => 40000,
                    'original_total_discounts_shop_amount' => 0,
                    'tbyb_refund_gross_sales_shop_amount' => 0,
                    'upfront_refund_gross_sales_shop_amount' => 0,
                    'tbyb_refund_discounts_shop_amount' => 0,
                    'upfront_refund_discounts_shop_amount' => 0,
                ],
                [
                    'status' => OrderStatus::COMPLETED,
                    'created_at' => CarbonImmutable::create(2024, 4, 30, 23, 59, 59),
                    'total_net_sales_shop_amount' => 40000,
                    'original_total_gross_sales_shop_amount' => 40000,
                    'tbyb_net_sales_shop_amount' => 40000,
                    'original_total_discounts_shop_amount' => 0,
                    'tbyb_refund_gross_sales_shop_amount' => 0,
                    'upfront_refund_gross_sales_shop_amount' => 0,
                    'tbyb_refund_discounts_shop_amount' => 0,
                    'upfront_refund_discounts_shop_amount' => 0,
                ],
                // This should be discarded
                [
                    'status' => OrderStatus::CANCELLED,
                    'created_at' => CarbonImmutable::create(2024, 4, 9, 0, 0, 0),
                    'total_net_sales_shop_amount' => 50000,
                    'original_total_gross_sales_shop_amount' => 50000,
                    'tbyb_net_sales_shop_amount' => 50000,
                    'original_total_discounts_shop_amount' => 0,
                    'tbyb_refund_gross_sales_shop_amount' => 0,
                    'upfront_refund_gross_sales_shop_amount' => 0,
                    'tbyb_refund_discounts_shop_amount' => 0,
                    'upfront_refund_discounts_shop_amount' => 0,
                ],
            ))->create([
                'store_id' => $this->store->id,
            ]);
        });

        $expectedOrders = [
            [
                'orderStatus' => OrderSummaryStatus::PROCESSING,
                'date' => CarbonImmutable::create(2024, 4, 1, 0, 0, 0),
                'orderCount' => 1,
                'grossSales' => 100,
                'netSales' => 100,
                'discounts' => 0,
                'productCost' => 25,
                'fulfillmentCost' => 8,
                'returnShippingCost' => 0,
                'paymentProcessingCost' => 2.3,
                'tbybFee' => 4,
                'returns' => 0,
                'profitContribution' => 60.7,
                'paidAdvertisingCost' => 0,
            ],
            [
                'orderStatus' => OrderSummaryStatus::PROCESSING,
                'date' => CarbonImmutable::create(2024, 4, 2, 0, 0, 0),
                'orderCount' => 1,
                'grossSales' => 100,
                'netSales' => 100,
                'discounts' => 0,
                'productCost' => 25,
                'fulfillmentCost' => 8,
                'returnShippingCost' => 0,
                'paymentProcessingCost' => 2.3,
                'tbybFee' => 4,
                'returns' => 0,
                'profitContribution' => 60.7,
                'paidAdvertisingCost' => 0,
            ],
            [
                'orderStatus' => OrderSummaryStatus::PROCESSING,
                'date' => CarbonImmutable::create(2024, 4, 3, 0, 0, 0),
                'orderCount' => 1,
                'grossSales' => 100,
                'netSales' => 100,
                'discounts' => 0,
                'productCost' => 25,
                'fulfillmentCost' => 8,
                'returnShippingCost' => 0,
                'paymentProcessingCost' => 2.3,
                'tbybFee' => 4,
                'returns' => 0,
                'profitContribution' => 60.7,
                'paidAdvertisingCost' => 0,
            ],
            [
                'orderStatus' => OrderSummaryStatus::PROCESSING,
                'date' => CarbonImmutable::create(2024, 4, 4, 0, 0, 0),
                'orderCount' => 1,
                'grossSales' => 100,
                'netSales' => 100,
                'discounts' => 0,
                'productCost' => 25,
                'fulfillmentCost' => 8,
                'returnShippingCost' => 0,
                'paymentProcessingCost' => 2.3,
                'tbybFee' => 4,
                'returns' => 0,
                'profitContribution' => 60.7,
                'paidAdvertisingCost' => 0,
            ],
            [
                'orderStatus' => OrderSummaryStatus::SHIPPED,
                'date' => CarbonImmutable::create(2024, 4, 5, 0, 0, 0),
                'orderCount' => 1,
                'grossSales' => 200,
                'netSales' => 200,
                'discounts' => 0,
                'productCost' => 50,
                'fulfillmentCost' => 8,
                'returnShippingCost' => 0,
                'paymentProcessingCost' => 4.6,
                'tbybFee' => 8,
                'returns' => 0,
                'profitContribution' => 129.4,
                'paidAdvertisingCost' => 0,
            ],
            [
                'orderStatus' => OrderSummaryStatus::SHIPPED,
                'date' => CarbonImmutable::create(2024, 4, 6, 0, 0, 0),
                'orderCount' => 1,
                'grossSales' => 200,
                'netSales' => 200,
                'discounts' => 0,
                'productCost' => 50,
                'fulfillmentCost' => 8,
                'returnShippingCost' => 0,
                'paymentProcessingCost' => 4.6,
                'tbybFee' => 8,
                'returns' => 0,
                'profitContribution' => 129.4,
                'paidAdvertisingCost' => 0,
            ],
            [
                'orderStatus' => OrderSummaryStatus::TRIAL_IN_PROGRESS,
                'date' => CarbonImmutable::create(2024, 4, 7, 0, 0, 0),
                'orderCount' => 1,
                'grossSales' => 300,
                'netSales' => 300,
                'discounts' => 0,
                'productCost' => 75,
                'fulfillmentCost' => 8,
                'returnShippingCost' => 0,
                'paymentProcessingCost' => 6.9,
                'tbybFee' => 12,
                'returns' => 0,
                'profitContribution' => 198.1,
                'paidAdvertisingCost' => 0,
            ],
            [
                'orderStatus' => OrderSummaryStatus::COMPLETED,
                'date' => CarbonImmutable::create(2024, 4, 8, 0, 0, 0),
                'orderCount' => 1,
                'grossSales' => 400,
                'netSales' => 400,
                'discounts' => 0,
                'productCost' => 100,
                'fulfillmentCost' => 8,
                'returnShippingCost' => 0,
                'paymentProcessingCost' => 9.2,
                'tbybFee' => 16,
                'returns' => 0,
                'profitContribution' => 266.8,
                'paidAdvertisingCost' => 0,
            ],
            [
                'orderStatus' => OrderSummaryStatus::COMPLETED,
                'date' => CarbonImmutable::create(2024, 4, 30, 23, 59, 59),
                'orderCount' => 1,
                'grossSales' => 400,
                'netSales' => 400,
                'discounts' => 0,
                'productCost' => 100,
                'fulfillmentCost' => 8,
                'returnShippingCost' => 0,
                'paymentProcessingCost' => 9.2,
                'tbybFee' => 16,
                'returns' => 0,
                'profitContribution' => 266.8,
                'paidAdvertisingCost' => 0,
            ],
        ];

        $analyticsData = $this->service->get();

        $this->validate($expectedOrders, $analyticsData);
    }

    public function testItGetsAnalyticsDataWithReturns(): void
    {
        $orderWithReturns = Order::withoutEvents(function () {
            return Order::factory()->create([
                'store_id' => $this->store->id,
                'status' => OrderStatus::IN_TRIAL,
                'created_at' => CarbonImmutable::create(2024, 4, 7, 0, 0, 0),
                'total_net_sales_shop_amount' => 30000,
                'original_total_gross_sales_shop_amount' => 30000,
                'tbyb_net_sales_shop_amount' => 30000,
                'original_total_discounts_shop_amount' => 0,
                'tbyb_refund_gross_sales_shop_amount' => 0, // returns don't contribute to refunds
                'upfront_refund_gross_sales_shop_amount' => 0,
                'tbyb_refund_discounts_shop_amount' => 0,
                'upfront_refund_discounts_shop_amount' => 0,
            ]);
        });
        OrderReturn::withoutEvents(function () use ($orderWithReturns) {
            return OrderReturn::factory()->create([
                'order_id' => $orderWithReturns->id,
                'store_id' => $this->store->id,
                'tbyb_gross_sales_shop_amount' => 10000,
                'tbyb_discounts_shop_amount' => 0,
                'upfront_gross_sales_shop_amount' => 0,
                'upfront_discounts_shop_amount' => 0,
                'tbyb_total_shop_amount' => 10000,
                'tbyb_tax_shop_amount' => 0,
                'upfront_tax_shop_amount' => 0,
            ]);
        });

        $orderWithRefunds = Order::withoutEvents(function () {
            return Order::factory()->create([
                'store_id' => $this->store->id,
                'status' => OrderStatus::IN_TRIAL,
                'created_at' => CarbonImmutable::create(2024, 4, 8, 0, 0, 0),
                'total_net_sales_shop_amount' => 25000,
                'original_total_gross_sales_shop_amount' => 40000,
                'tbyb_net_sales_shop_amount' => 25000,
                'original_total_discounts_shop_amount' => 0,
                'tbyb_refund_gross_sales_shop_amount' => 15000,
                'upfront_refund_gross_sales_shop_amount' => 0,
                'tbyb_refund_discounts_shop_amount' => 0,
                'upfront_refund_discounts_shop_amount' => 0,
            ]);
        });
        Refund::withoutEvents(function () use ($orderWithRefunds) {
            return Refund::factory()->create([
                'order_id' => $orderWithRefunds->id,
                'store_id' => $this->store->id,
                'tbyb_gross_sales_shop_amount' => 15000,
                'tbyb_gross_sales_customer_amount' => 15000,
                'tbyb_deposit_shop_amount' => 0,
                'tbyb_deposit_customer_amount' => 0,
                'tbyb_discounts_shop_amount' => 0,
                'tbyb_discounts_customer_amount' => 0,
                'tbyb_total_shop_amount' => 15000,
                'tbyb_total_customer_amount' => 15000,
                'upfront_gross_sales_shop_amount' => 0,
                'upfront_gross_sales_customer_amount' => 0,
                'upfront_discounts_shop_amount' => 0,
                'upfront_discounts_customer_amount' => 0,
                'upfront_total_shop_amount' => 0,
                'upfront_total_customer_amount' => 0,
            ]);
        });

        $expectedOrders = [
            [
                'orderStatus' => OrderSummaryStatus::TRIAL_IN_PROGRESS,
                'date' => CarbonImmutable::create(2024, 4, 7, 0, 0, 0),
                'orderCount' => 1,
                'grossSales' => 300,
                'netSales' => 300,
                'discounts' => 0,
                'productCost' => 75,
                'fulfillmentCost' => 8,
                'returnShippingCost' => 8,
                'paymentProcessingCost' => 6.9,
                'tbybFee' => 12,
                'returns' => 0, // line item returns don't contribute to returns, only refunds do
                'profitContribution' => 190.1,
                'paidAdvertisingCost' => 0,
            ],
            [
                'orderStatus' => OrderSummaryStatus::TRIAL_IN_PROGRESS,
                'date' => CarbonImmutable::create(2024, 4, 8, 0, 0, 0),
                'orderCount' => 1,
                'grossSales' => 400,
                'netSales' => 250,
                'discounts' => 0,
                'productCost' => 62.5,
                'fulfillmentCost' => 8,
                'returnShippingCost' => 8,
                'paymentProcessingCost' => 9.2,
                'tbybFee' => 10,
                'returns' => 150,
                'profitContribution' => 152.3,
                'paidAdvertisingCost' => 0,
            ],
        ];

        $analyticsData = $this->service->get();

        $this->validate($expectedOrders, $analyticsData);
    }

    private function validate(array $expectedOrders, AnalyticsData $analyticsData): void
    {
        $this->assertCount(count($expectedOrders), $analyticsData->data);

        foreach ($expectedOrders as $i => $expectedOrder) {
            $this->assertEquals($expectedOrder['date'], $analyticsData->data[$i]->date);
            $this->assertEquals($expectedOrder['orderStatus'], $analyticsData->data[$i]->orderStatus);
            $this->assertEquals($expectedOrder['orderCount'], $analyticsData->data[$i]->orderCount);
            $this->assertEquals($expectedOrder['grossSales'], $analyticsData->data[$i]->grossSales->getAmount()->toFloat());
            $this->assertEquals($expectedOrder['netSales'], $analyticsData->data[$i]->netSales->getAmount()->toFloat());
            $this->assertEquals($expectedOrder['discounts'], $analyticsData->data[$i]->discounts->getAmount()->toFloat());
            $this->assertEquals($expectedOrder['productCost'], $analyticsData->data[$i]->productCost->getAmount()->toFloat());
            $this->assertEquals($expectedOrder['fulfillmentCost'], $analyticsData->data[$i]->fulfillmentCost->getAmount()->toFloat());
            $this->assertEquals($expectedOrder['returnShippingCost'], $analyticsData->data[$i]->returnShippingCost->getAmount()->toFloat());
            $this->assertEquals($expectedOrder['paymentProcessingCost'], $analyticsData->data[$i]->paymentProcessingCost->getAmount()->toFloat());
            $this->assertEquals($expectedOrder['tbybFee'], $analyticsData->data[$i]->tbybFee->getAmount()->toFloat());
            $this->assertEquals($expectedOrder['returns'], $analyticsData->data[$i]->returns->getAmount()->toFloat());
            $this->assertEquals($expectedOrder['profitContribution'], $analyticsData->data[$i]->profitContribution->getAmount()->toFloat());
            $this->assertEquals($expectedOrder['paidAdvertisingCost'], $analyticsData->data[$i]->paidAdvertisingCost->getAmount()->toFloat());
        }
    }
}
