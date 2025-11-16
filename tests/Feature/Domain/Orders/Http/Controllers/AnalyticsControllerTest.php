<?php

declare(strict_types=1);

namespace Tests\Feature\Domain\Orders\Http\Controllers;

use App;
use App\Domain\Orders\Enums\OrderStatus;
use App\Domain\Orders\Models\Order;
use App\Domain\Shopify\Values\JwtPayload;
use App\Domain\Stores\Models\Store;
use App\Domain\Stores\Values\Store as StoreValue;
use Arr;
use Carbon\CarbonImmutable;
use Config;
use Firebase\JWT\JWT;
use Tests\TestCase;

class AnalyticsControllerTest extends TestCase
{
    private Store $currentStore;
    private array $headers;

    protected function setUp(): void
    {
        parent::setUp();

        CarbonImmutable::setTestNow(CarbonImmutable::create(2024, 6, 1));

        $this->currentStore = Store::withoutEvents(function () {
            return Store::factory()->create([
                'created_at' => CarbonImmutable::now()->subMonths(2),
            ]);
        });
        App::context(store: StoreValue::from($this->currentStore));

        Config::set('services.shopify.client_secret', 'super-secret-key');

        $this->headers = [
            'Authorization' => 'Bearer ' . JWT::encode(
                (new JwtPayload(domain: $this->currentStore->domain))->toArray(),
                config('services.shopify.client_secret'),
                'HS256'
            ),
        ];
    }

    public function testThatSuccessReturned(): void
    {
        $orders1 = Order::withoutEvents(function () {
            return Order::factory()->count(2)->create([
                'status' => OrderStatus::FULFILLMENT_PARTIALLY_FULFILLED,
                'store_id' => $this->currentStore->id,
                'created_at' => CarbonImmutable::now()->subDay(),
            ]);
        });
        $orders2 = Order::withoutEvents(function () {
            return Order::factory()->count(2)->create([
                'status' => OrderStatus::PAYMENT_AUTHORIZED,
                'store_id' => $this->currentStore->id,
                'created_at' => CarbonImmutable::now()->subDay(),
            ]);
        });
        Order::withoutEvents(function () {
            return Order::factory()->count(2)->create([
                'status' => OrderStatus::FULFILLMENT_FULFILLED,
                'store_id' => $this->currentStore->id,
                'created_at' => CarbonImmutable::now()->subMonths(3),
            ]);
        });
        $orders3 = Order::withoutEvents(function () {
            return Order::factory()->count(2)->create([
                'status' => OrderStatus::FULFILLMENT_FULFILLED,
                'store_id' => $this->currentStore->id,
                'created_at' => CarbonImmutable::now()->subDays(5),
            ]);
        });
        Order::withoutEvents(function () {
            return Order::factory()->count(3)->create([
                'status' => OrderStatus::PAYMENT_PAID,
                'store_id' => $this->currentStore->id,
                'created_at' => CarbonImmutable::now()->addMonths(3),
            ]);
        });
        $orders4 = Order::withoutEvents(function () {
            return Order::factory()->create([
                'status' => OrderStatus::PAYMENT_PAID,
                'store_id' => $this->currentStore->id,
                'created_at' => CarbonImmutable::now()->subDays(10),
            ]);
        });

        $response = $this->getJson(
            '/api/stores/orders/analytics',
            $this->headers
        );

        $response->assertStatus(200);
        $data = collect($response->json()['analytics']['data']);
        $this->assertCount(7, $data);
        $orders = Arr::collapse([$orders1, $orders2, $orders3, [$orders4]]);
        $expectedOrders = [];
        foreach ($orders as $record) {
            $expectedOrders[] = [
                'grossSales' => 100,
                'netSales' => 100,
                'discounts' => 0,
                'productCost' => 25,
                'fulfillmentCost' => 8,
                'returnShippingCost' => 0,
                'paymentProcessingCost' => 2.2999999999999998,
                'tbybFee' => 4,
                'returns' => 0,
                'profitContribution' => 60.70000000000000003,
                'paidAdvertisingCost' => 0,
            ];
        }
        $expectedOrders = collect($expectedOrders);
        $this->assertEquals($expectedOrders->sum('grossSales'), $data->sum('grossSales'));
        $this->assertEquals($expectedOrders->sum('netSales'), $data->sum('netSales'));
        $this->assertEquals($expectedOrders->sum('discounts'), $data->sum('discounts'));
        $this->assertEquals($expectedOrders->sum('productCost'), $data->sum('productCost'));
        $this->assertEquals($expectedOrders->sum('fulfillmentCost'), $data->sum('fulfillmentCost'));
        $this->assertEquals($expectedOrders->sum('returnShippingCost'), $data->sum('returnShippingCost'));
        $this->assertEquals($expectedOrders->sum('paymentProcessingCost'), $data->sum('paymentProcessingCost'));
        $this->assertEquals($expectedOrders->sum('tbybFee'), $data->sum('tbybFee'));
        $this->assertEquals($expectedOrders->sum('returns'), $data->sum('returns'));
        $this->assertEquals($expectedOrders->sum('profitContribution'), $data->sum('profitContribution'));
        $this->assertEquals($expectedOrders->sum('paidAdvertisingCost'), $data->sum('paidAdvertisingCost'));
    }
}
