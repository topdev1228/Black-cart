<?php
declare(strict_types=1);

namespace Tests\Unit\Domain\Payments\Services;

use App\Domain\Payments\Services\BlackcartOrderService;
use Illuminate\Http\Client\RequestException;
use Illuminate\Http\Response;
use Illuminate\Support\Facades\Http;
use Tests\TestCase;

class BlackcartOrderServiceTest extends TestCase
{
    public function testItGetsOrderById(): void
    {
        $expectedOrder = [
            'order' => [
                'store_id' => '9b8d8ea9-4be5-4f1c-a84e-24778925d624',
                'id' => '9b8d934b-5b91-40be-8ba8-55c5b9a542e7',
                'source_id' => 'gid://shopify/Order/42836199',
                'status' => 'open',
                'order_data' => [],
                'blackcart_metadata' => [],
                'name' => '#6970',
                'taxes_included' => false,
                'taxes_exempt' => false,
                'tags' => '',
                'discount_codes' => '',
                'test' => false,
                'payment_terms_id' => null,
                'payment_terms_name' => null,
                'payment_terms_type' => null,
                'shop_currency' => 'USD',
                'customer_currency' => 'USD',
                'total_shop_amount' => 10000,
                'total_customer_amount' => 10000,
                'outstanding_shop_amount' => 10000,
                'outstanding_customer_amount' => 10000,
                'original_outstanding_shop_amount' => 10000,
                'original_outstanding_customer_amount' => 10000,
                'original_tbyb_gross_sales_shop_amount' => 10000,
                'original_tbyb_gross_sales_customer_amount' => 10000,
                'original_upfront_gross_sales_shop_amount' => 0,
                'original_upfront_gross_sales_customer_amount' => 0,
                'original_total_gross_sales_shop_amount' => 10000,
                'original_total_gross_sales_customer_amount' => 10000,
                'original_tbyb_discounts_shop_amount' => 0,
                'original_tbyb_discounts_customer_amount' => 0,
                'original_upfront_discounts_shop_amount' => 0,
                'original_upfront_discounts_customer_amount' => 0,
                'original_total_discounts_shop_amount' => 0,
                'original_total_discounts_customer_amount' => 0,
                'tbyb_refund_gross_sales_shop_amount' => 0,
                'tbyb_refund_gross_sales_customer_amount' => 0,
                'upfront_refund_gross_sales_shop_amount' => 0,
                'upfront_refund_gross_sales_customer_amount' => 0,
                'total_order_level_refunds_shop_amount' => 0,
                'total_order_level_refunds_customer_amount' => 0,
                'tbyb_refund_discounts_shop_amount' => 0,
                'tbyb_refund_discounts_customer_amount' => 0,
                'upfront_refund_discounts_shop_amount' => 0,
                'upfront_refund_discounts_customer_amount' => 0,
                'tbyb_net_sales_shop_amount' => 10000,
                'tbyb_net_sales_customer_amount' => 10000,
                'upfront_net_sales_shop_amount' => 0,
                'upfront_net_sales_customer_amount' => 0,
                'total_net_sales_shop_amount' => 10000,
                'total_net_sales_customer_amount' => 10000,
                'completed_at' => null,
                'created_at' => '2024-03-13T09:53:58+00:00',
                'line_items' => [],
                'refunds' => [],
                'returns' => [],
                'transactions' => [],
            ],
        ];

        Http::fake([
            '*' => Http::response($expectedOrder, 200),
        ]);

        $blackcartOrderService = resolve(BlackcartOrderService::class);

        $order = $blackcartOrderService->getOrderById('9b8d934b-5b91-40be-8ba8-55c5b9a542e7');

        unset($expectedOrder['order']['returns'], $expectedOrder['order']['transactions']);

        $this->assertEquals($expectedOrder['order'], $order->toArray());
    }

    public function testItErrorsOnNotFound(): void
    {
        Http::fake([
            '*' => Http::response('', Response::HTTP_NOT_FOUND),
        ]);

        $this->expectException(RequestException::class);

        $blackcartOrderService = resolve(BlackcartOrderService::class);
        $blackcartOrderService->getOrderById('9b8d934b-5b91-40be-8ba8-55c5b9a542e7');
    }

    public function testItErrorsOnClientError(): void
    {
        Http::fake([
            '*' => Http::response('', Response::HTTP_BAD_REQUEST),
        ]);

        $this->expectException(RequestException::class);

        $blackcartOrderService = resolve(BlackcartOrderService::class);
        $blackcartOrderService->getOrderById('9b8d934b-5b91-40be-8ba8-55c5b9a542e7');
    }

    public function testItErrorsOnServerError(): void
    {
        Http::fake([
            '*' => Http::response('', Response::HTTP_INTERNAL_SERVER_ERROR),
        ]);

        $this->expectException(RequestException::class);

        $blackcartOrderService = resolve(BlackcartOrderService::class);
        $blackcartOrderService->getOrderById('9b8d934b-5b91-40be-8ba8-55c5b9a542e7');
    }

    public function testItErrorsOnAuthenticationError(): void
    {
        Http::fake([
            '*' => Http::response('', Response::HTTP_UNAUTHORIZED),
        ]);

        $this->expectException(RequestException::class);

        $blackcartOrderService = resolve(BlackcartOrderService::class);
        $blackcartOrderService->getOrderById('9b8d934b-5b91-40be-8ba8-55c5b9a542e7');
    }
}
