<?php
declare(strict_types=1);

namespace Feature\Domain\Orders\Listeners;

use App\Domain\Orders\Listeners\WebhookRefundsCreateListener;
use App\Domain\Orders\Services\RefundService;
use App\Domain\Orders\Values\WebhookRefundsCreate;
use App\Domain\Shared\Values\PubSubMessageEnvelope;
use App\Domain\Stores\Models\Store;
use Illuminate\Support\Facades\App;
use Mockery\MockInterface;
use Tests\TestCase;

class WebhookRefundsCreateListenerTest extends TestCase
{
    protected Store $currentStore;

    protected function setUp(): void
    {
        parent::setUp();

        $this->currentStore = Store::withoutEvents(function () {
            return Store::factory()->create();
        });
        App::context(store: $this->currentStore);
    }

    public function testItDoesNotCreateRefundNotFoundOrder(): void
    {
        $webhook = WebhookRefundsCreate::from(
            $this->loadFixtureData('order-refunds-create-capture-webhook.json', 'Orders')
        );

        $listener = resolve(WebhookRefundsCreateListener::class);
        $refundValue = $listener->handle($webhook);

        $this->assertNull($refundValue);
        $this->assertDatabaseEmpty('orders_refunds');
    }

    public function testItCallsHandler(): void
    {
        $this->mock(RefundService::class, function (MockInterface $mock) {
            $mock->shouldReceive('createFromWebhook')->withArgs(function (WebhookRefundsCreate $data) {
                return true;
            })->once();
        });

        $message = PubSubMessageEnvelope::builder()->shopifyWebhook()->create([
            'subscription' => 'shopify-orders-webhook-refunds-create',
        ])->toArray();
        $message['message']['data'] = base64_encode('{
            "id": 829190242433,
            "order_id": 4182097789057,
            "created_at": "2024-03-09T04:46:16-05:00",
            "note": "",
            "user_id": 73836855425,
            "processed_at": "2024-03-09T04:46:16-05:00",
            "restock": true,
            "duties": [],
            "total_duties_set": {
                "shop_money": {
                    "amount": "0.00",
                    "currency_code": "CAD"
                },
                "presentment_money": {
                    "amount": "0.00",
                    "currency_code": "USD"
                }
            },
            "additional_fees": [],
            "total_additional_fees_set": {
                "shop_money": {
                    "amount": "0.00",
                    "currency_code": "CAD"
                },
                "presentment_money": {
                    "amount": "0.00",
                    "currency_code": "USD"
                }
            },
            "return": null,
            "refund_shipping_lines": [],
            "admin_graphql_api_id": "gid:\/\/shopify\/Refund\/829190242433",
            "refund_line_items": [
                {
                    "id": 297514729601,
                    "quantity": 2,
                    "line_item_id": 10957452378241,
                    "location_id": 63239716993,
                    "restock_type": "cancel",
                    "subtotal": 1240.71,
                    "total_tax": 125.31,
                    "subtotal_set": {
                        "shop_money": {
                            "amount": "1240.71",
                            "currency_code": "CAD"
                        },
                        "presentment_money": {
                            "amount": "919.80",
                            "currency_code": "USD"
                        }
                    },
                    "total_tax_set": {
                        "shop_money": {
                            "amount": "125.31",
                            "currency_code": "CAD"
                        },
                        "presentment_money": {
                            "amount": "92.90",
                            "currency_code": "USD"
                        }
                    },
                    "line_item": {
                        "id": 10957452378241,
                        "variant_id": 39732921204865,
                        "title": "The Collection Snowboard: Liquid",
                        "quantity": 3,
                        "sku": "",
                        "variant_title": null,
                        "vendor": "Hydrogen Vendor",
                        "fulfillment_service": "manual",
                        "product_id": 6672585523329,
                        "requires_shipping": true,
                        "taxable": true,
                        "gift_card": false,
                        "name": "The Collection Snowboard: Liquid",
                        "variant_inventory_management": "shopify",
                        "properties": [],
                        "product_exists": true,
                        "fulfillable_quantity": 1,
                        "grams": 0,
                        "price": "689.28",
                        "total_discount": "0.00",
                        "fulfillment_status": null,
                        "price_set": {
                            "shop_money": {
                                "amount": "689.28",
                                "currency_code": "CAD"
                            },
                            "presentment_money": {
                                "amount": "511.00",
                                "currency_code": "USD"
                            }
                        },
                        "total_discount_set": {
                            "shop_money": {
                                "amount": "0.00",
                                "currency_code": "CAD"
                            },
                            "presentment_money": {
                                "amount": "0.00",
                                "currency_code": "USD"
                            }
                        },
                        "discount_allocations": [
                            {
                                "amount": "206.78",
                                "discount_application_index": 0,
                                "amount_set": {
                                    "shop_money": {
                                        "amount": "206.78",
                                        "currency_code": "CAD"
                                    },
                                    "presentment_money": {
                                        "amount": "153.30",
                                        "currency_code": "USD"
                                    }
                                }
                            }
                        ],
                        "duties": [],
                        "admin_graphql_api_id": "gid:\/\/shopify\/LineItem\/10957452378241",
                        "tax_lines": [
                            {
                                "title": "Washington State Tax",
                                "price": "120.97",
                                "rate": 0.065,
                                "channel_liable": false,
                                "price_set": {
                                    "shop_money": {
                                        "amount": "120.97",
                                        "currency_code": "CAD"
                                    },
                                    "presentment_money": {
                                        "amount": "89.68",
                                        "currency_code": "USD"
                                    }
                                }
                            },
                            {
                                "title": "Pierce County Tax",
                                "price": "0.00",
                                "rate": 0.0,
                                "channel_liable": false,
                                "price_set": {
                                    "shop_money": {
                                        "amount": "0.00",
                                        "currency_code": "CAD"
                                    },
                                    "presentment_money": {
                                        "amount": "0.00",
                                        "currency_code": "USD"
                                    }
                                }
                            },
                            {
                                "title": "Puyallup City Tax",
                                "price": "67.00",
                                "rate": 0.036,
                                "channel_liable": false,
                                "price_set": {
                                    "shop_money": {
                                        "amount": "67.00",
                                        "currency_code": "CAD"
                                    },
                                    "presentment_money": {
                                        "amount": "49.67",
                                        "currency_code": "USD"
                                    }
                                }
                            }
                        ]
                    }
                }
            ],
            "transactions": [
                {
                    "id": 5489812930689,
                    "order_id": 4182097789057,
                    "kind": "refund",
                    "gateway": "shopify_payments",
                    "status": "pending",
                    "message": "Refund submitted and processing. Learn more about \u003ca href=\"https:\/\/help.shopify.com\/en\/manual\/payments\/shopify-payments\/faq#how-long-does-it-take-for-my-customer-to-get-refunded\" target=\"_blank\"\u003erefunds\u003c\/a\u003e.",
                    "created_at": "2024-03-09T04:46:16-05:00",
                    "test": true,
                    "authorization": "",
                    "location_id": null,
                    "user_id": 73836855425,
                    "parent_id": 5489812865153,
                    "processed_at": "2024-03-09T04:46:16-05:00",
                    "device_id": null,
                    "error_code": null,
                    "source_name": "1830279",
                    "payment_details": {
                        "credit_card_bin": "424242",
                        "avs_result_code": "Y",
                        "cvv_result_code": "M",
                        "credit_card_number": "•••• •••• •••• 4242",
                        "credit_card_company": "Visa",
                        "buyer_action_info": null,
                        "credit_card_name": "Davey Shafik",
                        "credit_card_wallet": null,
                        "credit_card_expiration_month": 1,
                        "credit_card_expiration_year": 2038,
                        "payment_method_name": "visa"
                    },
                    "receipt": {},
                    "amount": "1012.70",
                    "currency": "USD",
                    "payments_refund_attributes": {
                        "status": "deferred",
                        "acquirer_reference_number": null
                    },
                    "payment_id": "#1057.3",
                    "total_unsettled_set": {
                        "presentment_money": {
                            "amount": "0.0",
                            "currency": "USD"
                        },
                        "shop_money": {
                            "amount": "0.0",
                            "currency": "CAD"
                        }
                    },
                    "manual_payment_gateway": false,
                    "admin_graphql_api_id": "gid:\/\/shopify\/OrderTransaction\/5489812930689"
                }
            ],
            "order_adjustments": [
                {
                    "id": 199576158337,
                    "order_id": 4182097789057,
                    "refund_id": 829190242433,
                    "amount": "1366.02",
                    "tax_amount": "0.00",
                    "kind": "refund_discrepancy",
                    "reason": "Refund discrepancy",
                    "amount_set": {
                        "shop_money": {
                            "amount": "1366.02",
                            "currency_code": "CAD"
                        },
                        "presentment_money": {
                            "amount": "1012.70",
                            "currency_code": "USD"
                        }
                    },
                    "tax_amount_set": {
                        "shop_money": {
                            "amount": "0.00",
                            "currency_code": "CAD"
                        },
                        "presentment_money": {
                            "amount": "0.00",
                            "currency_code": "USD"
                        }
                    }
                }
            ]
        }');

        $response = $this->postJson('/pubsub/stores/programs', $message);
        $response->assertOk();
    }
}
