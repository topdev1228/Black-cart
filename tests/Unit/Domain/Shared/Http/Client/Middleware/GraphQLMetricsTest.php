<?php
declare(strict_types=1);

namespace Tests\Unit\Domain\Shared\Http\Client\Middleware;

use App\Domain\Orders\GraphQL\Shopify\Stable\Operations\RefundCreate;
use App\Domain\Shared\Facades\AppMetrics;
use App\Domain\Shared\Services\MetricsService;
use Http;
use Mockery\MockInterface;
use Tests\Fixtures\Services\SpanFake;
use Tests\Fixtures\Services\TracerFake;
use Tests\TestCase;

class GraphQLMetricsTest extends TestCase
{
    public function testItAddsMetricsForShopify(): void
    {
        $span = $this->mock(SpanFake::class, function (MockInterface $mock) {
            $mock->shouldReceive('setTag')->once()->with('graphql.type', 'mutation');
            $mock->shouldReceive('setTag')->once()->with('graphql.name', 'RefundCreate');
            $mock->shouldReceive('setTag')->once()->with('graphql.sanitized_variables.orderId', 'gid://shopify/Order/12345');
            $mock->shouldReceive('setTag')->once()->with('graphql.sanitized_variables.note', config('shopify.order_refund_adjustment.staff_note'));
            $mock->shouldReceive('setTag')->once()->with('graphql.sanitized_variables.amount', 100);
            $mock->shouldReceive('setTag')->once()->with('graphql.sanitized_variables.gateway', 'shopify_payments');
            $mock->shouldReceive('isFinished')->once();
            $mock->shouldReceive('finish')->once();

            $mock->shouldReceive('setTag')->once()->with(
                'graphql.query',
                <<<'EOQ'
                mutation RefundCreate($orderId: ID!, $note: String!, $refundLineItems: [RefundLineItemInput!], $currency: CurrencyCode, $amount: Money!, $gateway: String!, $parentTransactionId: ID) {
                          __typename
                          refundCreate(
                            input: { orderId: $orderId, note: $note, refundLineItems: $refundLineItems, currency: $currency, transactions: [{ orderId: $orderId, kind: REFUND, amount: $amount, gateway: $gateway, parentId: $parentTransactionId }] }
                          ) {
                            __typename
                            userErrors {
                              __typename
                              field
                              message
                            }
                            refund {
                              __typename
                              id
                            }
                          }
                        }
                EOQ
            );
        });

        $tracer = $this->mock(TracerFake::class, function (MockInterface $mock) use ($span) {
            $mock->shouldReceive('startActiveSpan')->once()->with('shopify.graphql', []);
            $mock->shouldReceive('getActiveSpan')->once()->andReturn($span);
        });

        $this->instance(MetricsService::class, new MetricsService($tracer));

        Http::fake([
            '*' => Http::response(),
        ]);

        Http::post('https://example.myshopify.com/graphql.json', [
            'query' => RefundCreate::document(),
            'variables' => ['orderId' => 'gid://shopify/Order/12345', 'note' => config('shopify.order_refund_adjustment.staff_note'), 'amount' => 100, 'gateway' => 'shopify_payments'],
        ]);
    }

    public function testItAddsMetricsForShopifyWithoutVariables(): void
    {
        $span = $this->mock(SpanFake::class, function (MockInterface $mock) {
            $mock->shouldReceive('setTag')->once()->with('graphql.type', 'mutation');
            $mock->shouldReceive('setTag')->once()->with('graphql.name', 'RefundCreate');
            $mock->shouldReceive('setTag')->once()->with('graphql.sanitized_variables', 'none');
            $mock->shouldReceive('isFinished')->once();
            $mock->shouldReceive('finish')->once();

            $mock->shouldReceive('setTag')->once()->with(
                'graphql.query',
                <<<'EOQ'
                mutation RefundCreate($orderId: ID!, $note: String!, $refundLineItems: [RefundLineItemInput!], $currency: CurrencyCode, $amount: Money!, $gateway: String!, $parentTransactionId: ID) {
                          __typename
                          refundCreate(
                            input: { orderId: $orderId, note: $note, refundLineItems: $refundLineItems, currency: $currency, transactions: [{ orderId: $orderId, kind: REFUND, amount: $amount, gateway: $gateway, parentId: $parentTransactionId }] }
                          ) {
                            __typename
                            userErrors {
                              __typename
                              field
                              message
                            }
                            refund {
                              __typename
                              id
                            }
                          }
                        }
                EOQ
            );
        });

        $tracer = $this->mock(TracerFake::class, function (MockInterface $mock) use ($span) {
            $mock->shouldReceive('startActiveSpan')->once()->with('shopify.graphql', []);
            $mock->shouldReceive('getActiveSpan')->once()->andReturn($span);
        });

        $this->instance(MetricsService::class, new MetricsService($tracer));

        Http::fake([
            '*' => Http::response(),
        ]);

        Http::post('https://example.myshopify.com/graphql.json', [
            'query' => RefundCreate::document(),
        ]);
    }

    public function testItAddsMetricsForShopifyWithUnsafeVariables(): void
    {
        $span = $this->mock(SpanFake::class, function (MockInterface $mock) {
            $mock->shouldReceive('setTag')->once()->with('graphql.type', 'mutation');
            $mock->shouldReceive('setTag')->once()->with('graphql.name', 'RefundCreate');
            $mock->shouldReceive('setTag')->once()->with('graphql.sanitized_variables', 'none');
            $mock->shouldReceive('setTag')->never()->with('graphql.sanitized_variables.email', 'davey@blackcart.co');
            $mock->shouldReceive('setTag')->never()->with('graphql.sanitized_variables.name', 'Davey Shafik');
            $mock->shouldReceive('isFinished')->once();
            $mock->shouldReceive('finish')->once();

            $mock->shouldReceive('setTag')->once()->with(
                'graphql.query',
                <<<'EOQ'
                mutation RefundCreate($orderId: ID!, $note: String!, $refundLineItems: [RefundLineItemInput!], $currency: CurrencyCode, $amount: Money!, $gateway: String!, $parentTransactionId: ID) {
                          __typename
                          refundCreate(
                            input: { orderId: $orderId, note: $note, refundLineItems: $refundLineItems, currency: $currency, transactions: [{ orderId: $orderId, kind: REFUND, amount: $amount, gateway: $gateway, parentId: $parentTransactionId }] }
                          ) {
                            __typename
                            userErrors {
                              __typename
                              field
                              message
                            }
                            refund {
                              __typename
                              id
                            }
                          }
                        }
                EOQ
            );
        });

        $tracer = $this->mock(TracerFake::class, function (MockInterface $mock) use ($span) {
            $mock->shouldReceive('startActiveSpan')->once()->with('shopify.graphql', []);
            $mock->shouldReceive('getActiveSpan')->once()->andReturn($span);
        });

        $this->instance(MetricsService::class, new MetricsService($tracer));

        Http::fake([
            '*' => Http::response(),
        ]);

        Http::post('https://example.myshopify.com/graphql.json', [
            'query' => RefundCreate::document(),
            'variables' => ['email' => 'davey@blackcart.co', 'name' => 'Davey Shafik'],
        ]);
    }

    public function testItAddsMetricsForShopifyWithSomeUnsafeVariables(): void
    {
        $span = $this->mock(SpanFake::class, function (MockInterface $mock) {
            $mock->shouldReceive('setTag')->once()->with('graphql.type', 'mutation');
            $mock->shouldReceive('setTag')->once()->with('graphql.name', 'RefundCreate');
            $mock->shouldReceive('setTag')->once()->with('graphql.sanitized_variables.test', 'safe');
            $mock->shouldReceive('setTag')->never()->with('graphql.sanitized_variables.email', 'davey@blackcart.co');
            $mock->shouldReceive('setTag')->never()->with('graphql.sanitized_variables.name', 'Davey Shafik');
            $mock->shouldReceive('isFinished')->once();
            $mock->shouldReceive('finish')->once();

            $mock->shouldReceive('setTag')->once()->with(
                'graphql.query',
                <<<'EOQ'
                mutation RefundCreate($orderId: ID!, $note: String!, $refundLineItems: [RefundLineItemInput!], $currency: CurrencyCode, $amount: Money!, $gateway: String!, $parentTransactionId: ID) {
                          __typename
                          refundCreate(
                            input: { orderId: $orderId, note: $note, refundLineItems: $refundLineItems, currency: $currency, transactions: [{ orderId: $orderId, kind: REFUND, amount: $amount, gateway: $gateway, parentId: $parentTransactionId }] }
                          ) {
                            __typename
                            userErrors {
                              __typename
                              field
                              message
                            }
                            refund {
                              __typename
                              id
                            }
                          }
                        }
                EOQ
            );
        });

        $tracer = $this->mock(TracerFake::class, function (MockInterface $mock) use ($span) {
            $mock->shouldReceive('startActiveSpan')->once()->with('shopify.graphql', []);
            $mock->shouldReceive('getActiveSpan')->once()->andReturn($span);
        });

        $this->instance(MetricsService::class, new MetricsService($tracer));

        Http::fake([
            '*' => Http::response(),
        ]);

        Http::post('https://example.myshopify.com/graphql.json', [
            'query' => RefundCreate::document(),
            'variables' => ['test' => 'safe', 'email' => 'davey@blackcart.co', 'name' => 'Davey Shafik'],
        ]);
    }

    public function testItAddsMetricsForOther(): void
    {
        $span = $this->mock(SpanFake::class, function (MockInterface $mock) {
            $mock->shouldReceive('setTag')->once()->with('graphql.type', 'mutation');
            $mock->shouldReceive('setTag')->once()->with('graphql.name', 'RefundCreate');
            $mock->shouldReceive('setTag')->once()->with('graphql.sanitized_variables.orderId', 'gid://shopify/Order/12345');
            $mock->shouldReceive('setTag')->once()->with('graphql.sanitized_variables.note', config('shopify.order_refund_adjustment.staff_note'));
            $mock->shouldReceive('setTag')->once()->with('graphql.sanitized_variables.amount', 100);
            $mock->shouldReceive('setTag')->once()->with('graphql.sanitized_variables.gateway', 'shopify_payments');
            $mock->shouldReceive('isFinished')->once();
            $mock->shouldReceive('finish')->once();

            $mock->shouldReceive('setTag')->once()->with(
                'graphql.query',
                <<<'EOQ'
                mutation RefundCreate($orderId: ID!, $note: String!, $refundLineItems: [RefundLineItemInput!], $currency: CurrencyCode, $amount: Money!, $gateway: String!, $parentTransactionId: ID) {
                          __typename
                          refundCreate(
                            input: { orderId: $orderId, note: $note, refundLineItems: $refundLineItems, currency: $currency, transactions: [{ orderId: $orderId, kind: REFUND, amount: $amount, gateway: $gateway, parentId: $parentTransactionId }] }
                          ) {
                            __typename
                            userErrors {
                              __typename
                              field
                              message
                            }
                            refund {
                              __typename
                              id
                            }
                          }
                        }
                EOQ
            );
        });

        $tracer = $this->mock(TracerFake::class, function (MockInterface $mock) use ($span) {
            $mock->shouldReceive('startActiveSpan')->once()->with('other.graphql', []);
            $mock->shouldReceive('getActiveSpan')->once()->andReturn($span);
        });

        $this->instance(MetricsService::class, new MetricsService($tracer));

        Http::fake([
            '*' => Http::response(),
        ]);

        Http::post('https://example.notshopify.com/graphql.json', [
            'query' => RefundCreate::document(),
            'variables' => ['orderId' => 'gid://shopify/Order/12345', 'note' => config('shopify.order_refund_adjustment.staff_note'), 'amount' => 100, 'gateway' => 'shopify_payments'],
        ]);
    }

    public function testItDoesNotAddMetrics(): void
    {
        AppMetrics::shouldReceive('startSpan')->never();

        Http::fake([
            '*' => Http::response(),
        ]);

        Http::post('https://example.notshopify.com/notql.json', [
            'query' => RefundCreate::document(),
            'variables' => ['orderId' => 'gid://shopify/Order/12345', 'note' => 'Test', 'amount' => 100, 'gateway' => 'shopify_payments'],
        ]);
    }
}
