<?php
declare(strict_types=1);

namespace Tests\Feature\Domain\Orders\Listeners;

use App\Domain\Orders\Listeners\AddOrderRefundAdjustmentListener;
use App\Domain\Orders\Models\Order;
use App\Domain\Orders\Values\Refund;
use App\Domain\Orders\Values\RefundCreatedEvent;
use App\Domain\Shared\Exceptions\Shopify\ShopifyServerException;
use App\Domain\Stores\Models\Store;
use Feature;
use Illuminate\Support\Facades\App;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Str;
use Tests\TestCase;

class AddOrderRefundAdjustmentListenerTest extends TestCase
{
    public function testItCanBeKilled(): void
    {
        Feature::fake(['shopify-perm-b-kill-fix-shopify-outstanding-balance-adjustments']);

        $store = Store::factory()->create();
        App::context(store: $store);

        $order = Order::factory()->create(['store_id' => $store->id]);

        $listener = app(AddOrderRefundAdjustmentListener::class);
        $event = RefundCreatedEvent::builder()->create([
            'refund' => Refund::builder()->create([
                'order_id' => $order->id,
                'refunded_customer_amount' => 10000,
            ]),
        ]);

        $listener->handle($event);

        Http::assertNothingSent();
    }

    public function testItDoesNotCreateAdjustmentOnZeroRefundedAmount(): void
    {
        $store = Store::factory()->create();
        App::context(store: $store);

        $order = Order::factory()->create(['store_id' => $store->id]);

        $listener = app(AddOrderRefundAdjustmentListener::class);
        $event = RefundCreatedEvent::builder()->create([
            'refund' => Refund::builder()->create([
                'order_id' => $order->id,
                'refunded_customer_amount' => 0,
            ]),
        ]);

        $listener->handle($event);

        Http::assertNothingSent();
    }

    public function testItDoesNotCreateAdjustmentOnNegativeRefundedAmount(): void
    {
        $store = Store::factory()->create();
        App::context(store: $store);

        $order = Order::factory()->create(['store_id' => $store->id]);

        $listener = app(AddOrderRefundAdjustmentListener::class);
        $event = RefundCreatedEvent::builder()->create([
            'refund' => Refund::builder()->create([
                'order_id' => $order->id,
                'refunded_customer_amount' => -1000,
            ]),
        ]);

        $listener->handle($event);

        Http::assertNothingSent();
    }

    public function testItCreatesAdjustments(): void
    {
        $store = Store::factory()->create();
        App::context(store: $store);

        $order = Order::factory()->create(['store_id' => $store->id, 'shop_currency' => 'CAD', 'source_id' => 'gid://shopify/Order/467284042']);

        $listener = app(AddOrderRefundAdjustmentListener::class);
        $event = RefundCreatedEvent::builder()->create([
            'refund' => Refund::builder()->create([
                'order_id' => $order->id,
                'refunded_customer_amount' => 10000,
            ]),
        ]);

        Http::fake([
            '*' => Http::sequence()
                ->push([
                    'data' => [
                        'orderEditBegin' => [
                            'calculatedOrder' => [
                                'id' => Str::shopifyGid(Str::uuid(), 'CalculatedOrder'),
                            ],
                        ],
                    ],
                ])
                ->push([
                    'data' => [
                        'orderEditAddCustomItem' => [
                            'calculatedLineItem' => [
                                'id' => 'gid://shopify/CalculatedLineItem/2ddbfc8e-bdc7-405d-b1a2-f0870d3d4b8f',
                            ],
                            'userErrors' => [],
                        ],
                    ],
                    'extensions' => [
                        'cost' => [
                            'requestedQueryCost' => 10,
                            'actualQueryCost' => 10,
                            'throttleStatus' => [
                                'maximumAvailable' => 2000.0,
                                'currentlyAvailable' => 1990,
                                'restoreRate' => 100.0,
                            ],
                        ],
                    ],
                ])
                ->push([
                    'data' => [
                        'orderEditCommit' => [
                            'order' => [
                                'id' => 'gid://shopify/Order/4188101345409',
                                'lineItems' => [
                                    'nodes' => [
                                        [
                                            'id' => 'gid://shopify/LineItem/10972956557441',
                                            'product' => [
                                                'id' => 'gid://shopify/Product/6672585523329',
                                            ],
                                            'variant' => [
                                                'id' => 'gid://shopify/ProductVariant/39732921204865',
                                            ],
                                            'title' => 'The Collection Snowboard: Liquid',
                                            'variantTitle' => null,
                                            'image' => [
                                                'url' => 'https://cdn.shopify.com/s/files/1/0568/0739/1361/products/Main_b13ad453-477c-4ed1-9b43-81f3345adfd6.jpg?v=1707514531',
                                            ],
                                            'quantity' => 1,
                                            'originalUnitPriceSet' => [
                                                'shopMoney' => [
                                                    'amount' => '688.64',
                                                    'currencyCode' => 'CAD',
                                                ],
                                                'presentmentMoney' => [
                                                    'amount' => '507.0',
                                                    'currencyCode' => 'USD',
                                                ],
                                            ],
                                            'originalTotalSet' => [
                                                'shopMoney' => [
                                                    'amount' => '688.64',
                                                    'currencyCode' => 'CAD',
                                                ],
                                                'presentmentMoney' => [
                                                    'amount' => '507.0',
                                                    'currencyCode' => 'USD',
                                                ],
                                            ],
                                            'discountAllocations' => [],
                                            'taxLines' => [
                                                [
                                                    'rate' => 0.065,
                                                    'ratePercentage' => 6.5,
                                                    'priceSet' => [
                                                        'shopMoney' => [
                                                            'amount' => '44.77',
                                                            'currencyCode' => 'CAD',
                                                        ],
                                                        'presentmentMoney' => [
                                                            'amount' => '32.96',
                                                            'currencyCode' => 'USD',
                                                        ],
                                                    ],
                                                ],
                                                [
                                                    'rate' => 0.036,
                                                    'ratePercentage' => 3.6,
                                                    'priceSet' => [
                                                        'shopMoney' => [
                                                            'amount' => '24.79',
                                                            'currencyCode' => 'CAD',
                                                        ],
                                                        'presentmentMoney' => [
                                                            'amount' => '18.25',
                                                            'currencyCode' => 'USD',
                                                        ],
                                                    ],
                                                ],
                                                [
                                                    'rate' => 0.0,
                                                    'ratePercentage' => 0.0,
                                                    'priceSet' => [
                                                        'shopMoney' => [
                                                            'amount' => '0.0',
                                                            'currencyCode' => 'CAD',
                                                        ],
                                                        'presentmentMoney' => [
                                                            'amount' => '0.0',
                                                            'currencyCode' => 'USD',
                                                        ],
                                                    ],
                                                ],
                                            ],
                                        ],
                                        [
                                            'id' => 'gid://shopify/LineItem/10974668128385',
                                            'product' => null,
                                            'variant' => null,
                                            'title' => 'Blackcart TBYB Adjustment',
                                            'variantTitle' => null,
                                            'image' => null,
                                            'quantity' => 1,
                                            'originalUnitPriceSet' => [
                                                'shopMoney' => [
                                                    'amount' => '271.86',
                                                    'currencyCode' => 'CAD',
                                                ],
                                                'presentmentMoney' => [
                                                    'amount' => '200.0',
                                                    'currencyCode' => 'USD',
                                                ],
                                            ],
                                            'originalTotalSet' => [
                                                'shopMoney' => [
                                                    'amount' => '271.86',
                                                    'currencyCode' => 'CAD',
                                                ],
                                                'presentmentMoney' => [
                                                    'amount' => '200.0',
                                                    'currencyCode' => 'USD',
                                                ],
                                            ],
                                            'discountAllocations' => [],
                                            'taxLines' => [
                                                [
                                                    'rate' => 0.036,
                                                    'ratePercentage' => 3.6,
                                                    'priceSet' => [
                                                        'shopMoney' => [
                                                            'amount' => '0.0',
                                                            'currencyCode' => 'CAD',
                                                        ],
                                                        'presentmentMoney' => [
                                                            'amount' => '0.0',
                                                            'currencyCode' => 'USD',
                                                        ],
                                                    ],
                                                ],
                                                [
                                                    'rate' => 0.0,
                                                    'ratePercentage' => 0.0,
                                                    'priceSet' => [
                                                        'shopMoney' => [
                                                            'amount' => '0.0',
                                                            'currencyCode' => 'CAD',
                                                        ],
                                                        'presentmentMoney' => [
                                                            'amount' => '0.0',
                                                            'currencyCode' => 'USD',
                                                        ],
                                                    ],
                                                ],
                                                [
                                                    'rate' => 0.065,
                                                    'ratePercentage' => 6.5,
                                                    'priceSet' => [
                                                        'shopMoney' => [
                                                            'amount' => '0.0',
                                                            'currencyCode' => 'CAD',
                                                        ],
                                                        'presentmentMoney' => [
                                                            'amount' => '0.0',
                                                            'currencyCode' => 'USD',
                                                        ],
                                                    ],
                                                ],
                                            ],
                                        ],
                                        [
                                            'id' => 'gid://shopify/LineItem/10974668456065',
                                            'product' => null,
                                            'variant' => null,
                                            'title' => 'Blackcart TBYB Adjustment',
                                            'variantTitle' => null,
                                            'image' => null,
                                            'quantity' => 1,
                                            'originalUnitPriceSet' => [
                                                'shopMoney' => [
                                                    'amount' => '135.93',
                                                    'currencyCode' => 'CAD',
                                                ],
                                                'presentmentMoney' => [
                                                    'amount' => '100.0',
                                                    'currencyCode' => 'USD',
                                                ],
                                            ],
                                            'originalTotalSet' => [
                                                'shopMoney' => [
                                                    'amount' => '135.93',
                                                    'currencyCode' => 'CAD',
                                                ],
                                                'presentmentMoney' => [
                                                    'amount' => '100.0',
                                                    'currencyCode' => 'USD',
                                                ],
                                            ],
                                            'discountAllocations' => [],
                                            'taxLines' => [
                                                [
                                                    'rate' => 0.036,
                                                    'ratePercentage' => 3.6,
                                                    'priceSet' => [
                                                        'shopMoney' => [
                                                            'amount' => '0.0',
                                                            'currencyCode' => 'CAD',
                                                        ],
                                                        'presentmentMoney' => [
                                                            'amount' => '0.0',
                                                            'currencyCode' => 'USD',
                                                        ],
                                                    ],
                                                ],
                                                [
                                                    'rate' => 0.0,
                                                    'ratePercentage' => 0.0,
                                                    'priceSet' => [
                                                        'shopMoney' => [
                                                            'amount' => '0.0',
                                                            'currencyCode' => 'CAD',
                                                        ],
                                                        'presentmentMoney' => [
                                                            'amount' => '0.0',
                                                            'currencyCode' => 'USD',
                                                        ],
                                                    ],
                                                ],
                                                [
                                                    'rate' => 0.065,
                                                    'ratePercentage' => 6.5,
                                                    'priceSet' => [
                                                        'shopMoney' => [
                                                            'amount' => '0.0',
                                                            'currencyCode' => 'CAD',
                                                        ],
                                                        'presentmentMoney' => [
                                                            'amount' => '0.0',
                                                            'currencyCode' => 'USD',
                                                        ],
                                                    ],
                                                ],
                                            ],
                                        ],
                                    ],
                                ],
                            ],
                            'userErrors' => [],
                        ],
                    ],
                    'extensions' => [
                        'cost' => [
                            'requestedQueryCost' => 66,
                            'actualQueryCost' => 30,
                            'throttleStatus' => [
                                'maximumAvailable' => 2000.0,
                                'currentlyAvailable' => 1970,
                                'restoreRate' => 100.0,
                            ],
                        ],
                    ],
                ]),
        ]);

        $listener->handle($event);

        Http::assertSequencesAreEmpty();

        $this->assertDatabaseCount('orders_line_items', 1);
        $this->assertDatabaseHas('orders_line_items', [
            'order_id' => $order->id,
            'source_order_id' => 'gid://shopify/Order/467284042',
            'source_id' => 'gid://shopify/LineItem/10974668456065',
            'source_product_id' => null,
            'source_variant_id' => null,
            'product_title' => 'Blackcart TBYB Adjustment',
            'variant_title' => null,
            'thumbnail' => null,
            'quantity' => 1,
            'status' => 'internal',
            'decision_status' => 'internal',
            'trialable_id' => null,
            'trial_group_id' => null,
            'is_tbyb' => 0,
            'selling_plan_id' => null,
            'deposit_type' => null,
            'deposit_value' => null,
            'shop_currency' => 'CAD',
            'customer_currency' => 'USD',
            'price_shop_amount' => 13593,
            'price_customer_amount' => 10000,
            'total_price_shop_amount' => 13593,
            'total_price_customer_amount' => 10000,
            'discount_shop_amount' => 0,
            'discount_customer_amount' => 0,
            'tax_shop_amount' => 0,
            'tax_customer_amount' => 0,
            'deposit_shop_amount' => 0,
            'deposit_customer_amount' => 0,
        ]);
    }

    public function testItErrorsOnFailure(): void
    {
        $store = Store::factory()->create();
        App::context(store: $store);

        $order = Order::factory()->create(['store_id' => $store->id, 'shop_currency' => 'CAD', 'source_id' => 'gid://shopify/Order/467284042']);

        $listener = app(AddOrderRefundAdjustmentListener::class);
        $event = RefundCreatedEvent::builder()->create([
            'refund' => Refund::builder()->create([
                'order_id' => $order->id,
                'refunded_customer_amount' => 10000,
            ]),
        ]);

        Http::fake([
            '*' => Http::sequence()
                ->push([], 500),
        ]);

        $this->expectException(ShopifyServerException::class);

        $listener->handle($event);
    }

    public function testItFailsOnOrderCannotBeEditedError(): void
    {
        $store = Store::factory()->create();
        App::context(store: $store);

        $order = Order::factory()->create(['store_id' => $store->id, 'shop_currency' => 'CAD', 'source_id' => 'gid://shopify/Order/467284042']);

        $listener = app(AddOrderRefundAdjustmentListener::class);
        $event = RefundCreatedEvent::builder()->create([
            'refund' => Refund::builder()->create([
                'order_id' => $order->id,
                'refunded_customer_amount' => 10000,
            ]),
        ]);

        Http::fake([
            '*' => Http::response(
                body: [
                    'data' => [
                        '__typename' => 'Mutation',
                        'orderEditBegin' => [
                            'userErrors' => [
                                [
                                    '__typename' => 'UserError',
                                    'field' => [],
                                    'message' => 'The order cannot be edited.',
                                ],
                            ],
                        ],
                    ],
                ],
            ),
        ]);

        $listener->handle($event);

        $this->assertDatabaseEmpty('orders_line_items');
    }
}
