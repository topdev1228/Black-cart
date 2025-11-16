<?php
declare(strict_types=1);

namespace Tests\Feature\Domain\Orders\Listeners;

use App;
use App\Domain\Orders\Enums\OrderStatus;
use App\Domain\Orders\Listeners\InitialAuthFailedListener;
use App\Domain\Orders\Mail\AuthFailedOrderCancelled;
use App\Domain\Orders\Models\LineItem;
use App\Domain\Orders\Models\Order;
use App\Domain\Orders\Values\InitialAuthFailedEvent;
use App\Domain\Stores\Models\Store;
use App\Domain\Stores\Values\Store as StoreValue;
use Illuminate\Database\Eloquent\Factories\Sequence;
use Illuminate\Support\Facades\Http;
use Mail;
use Tests\TestCase;

class InitialAuthFailedListenerTest extends TestCase
{
    protected StoreValue $currentStore;
    protected Order $order;

    protected function setUp(): void
    {
        parent::setUp();
        Http::fake([
            'http://localhost:8080/api/stores/settings' => Http::response([
                'settings' => [
                    'customerSupportEmail' => [
                        'value' => 'customersupport@merchant.com',
                    ],
                ],
            ], 200),
        ]);
        $this->currentStore = StoreValue::from(Store::withoutEvents(function () {
            return Store::factory()->create();
        }));
        App::context(store: $this->currentStore);

        $this->order = Order::withoutEvents(function () {
            return Order::factory()->create([
                'store_id' => $this->currentStore->id,
                'payment_terms_id' => '123456789',
                'order_data' => [
                    'line_items' => [
                        [
                            'admin_graphql_api_id' => 'gid://shopify/LineItem/12878423851147',
                        ],
                        [
                            'admin_graphql_api_id' => 'gid://shopify/LineItem/12878423883915',
                        ],
                        [
                            'admin_graphql_api_id' => 'gid://shopify/LineItem/0987654567654',
                        ],
                    ],
                    'email' => 'matthew+test@blackcart.com',
                    'customer' => [
                        'first_name' => 'Matthew',
                    ],
                    'name' => '#1001',
                ],
            ]);
        });

        LineItem::factory()->count(3)->state(new Sequence(
            [
                'product_title' => 'The Collection Snowboard: Hydrogen',
                'variant_title' => 'Blue',
                'source_id' => 'gid://shopify/LineItem/12878423851147',
                'trialable_id' => '12345',
                'is_tbyb' => true,
            ],
            [
                'product_title' => 'The Collection Snowboard: Oxygen',
                'variant_title' => 'Black',
                'source_id' => 'gid://shopify/LineItem/12878423883915',
                'trialable_id' => '55555',
                'is_tbyb' => true,
            ],
            [
                'product_title' => 'The Collection Snowboard: Liquid',
                'variant_title' => 'Beige',
                'source_id' => 'gid://shopify/LineItem/0987654567654',
                'trialable_id' => null,
                'is_tbyb' => false,
            ],
        ))
            ->for($this->order)
            ->create();
    }

    public function testItCancelsOrder(): void
    {
        Mail::fake();

        Http::fake([
            App::context()->store->domain . '/admin/api/*/graphql.json' => Http::sequence()
                ->push([
                    'data' => [
                        'orderCancel' => [
                            'job' => [
                                'id' => 'gid://shopify/Job/7188f4d3-b3ab-4980-8b0d-807f0576c4d9',
                                'done' => true,
                            ],
                            'orderCancelUserErrors' => [
                            ],
                        ],
                    ],
                    'extensions' => [
                        'cost' => [
                            'requestedQueryCost' => 10,
                            'actualQueryCost' => 10,
                            'throttleStatus' => [
                                'maximumAvailable' => 2000,
                                'currentlyAvailable' => 1990,
                                'restoreRate' => 100,
                            ],
                        ],
                    ],
                ]),
        ]);

        $listener = resolve(InitialAuthFailedListener::class);
        $listener->handle(new InitialAuthFailedEvent($this->order->id));

        $this->assertEquals(OrderStatus::CANCELLED, $this->order->refresh()->status);

        Mail::assertSent(AuthFailedOrderCancelled::class);
    }

    public function testItDoesntCancelOrderOnJobFail(): void
    {
        Mail::fake();

        Http::fake([
            App::context()->store->domain . '/admin/api/*/graphql.json' => Http::sequence()
                ->push([
                    'data' => [
                        'orderCancel' => [
                            'job' => [
                            ],
                            'orderCancelUserErrors' => [
                                [
                                    'type' => 'invalid_iput',
                                ],
                            ],
                        ],
                    ],
                    'extensions' => [
                        'cost' => [
                            'requestedQueryCost' => 10,
                            'actualQueryCost' => 10,
                            'throttleStatus' => [
                                'maximumAvailable' => 2000,
                                'currentlyAvailable' => 1990,
                                'restoreRate' => 100,
                            ],
                        ],
                    ],
                ]),
        ]);

        $listener = resolve(InitialAuthFailedListener::class);
        $listener->handle(new InitialAuthFailedEvent($this->order->id));

        $this->assertNotEquals(OrderStatus::CANCELLED, $this->order->refresh()->status);

        Mail::assertNotSent(AuthFailedOrderCancelled::class);
    }

    public function testItCancelsOrderOnSuccessfulPoll(): void
    {
        Mail::fake();

        $jobPending = [
            'data' => [
                'job' => [
                    'id' => 'gid://shopify/Job/7188f4d3-b3ab-4980-8b0d-807f0576c4d9',
                    'done' => true,
                ],
            ],
        ];

        Http::fake([
            App::context()->store->domain . '/admin/api/*/graphql.json' => Http::sequence()
                ->push([
                    'data' => [
                        'orderCancel' => [
                            'job' => [
                                'id' => 'gid://shopify/Job/7188f4d3-b3ab-4980-8b0d-807f0576c4d9',
                                'done' => false,
                            ],
                            'orderCancelUserErrors' => [
                            ],
                        ],
                    ],
                    'extensions' => [
                    ],
                ])
                ->push($jobPending),
        ]);

        $listener = resolve(InitialAuthFailedListener::class);
        $listener->handle(new InitialAuthFailedEvent($this->order->id));

        $this->assertEquals(OrderStatus::CANCELLED, $this->order->refresh()->status);

        Mail::assertSent(AuthFailedOrderCancelled::class);
    }

    public function testItDoesntCancelOrderOnPollTimeout(): void
    {
        Mail::fake();

        $jobPending = [
            'data' => [
                'job' => [
                    'id' => 'gid://shopify/Job/7188f4d3-b3ab-4980-8b0d-807f0576c4d9',
                    'done' => false,
                ],
            ],
        ];

        Http::fake([
            App::context()->store->domain . '/admin/api/*/graphql.json' => Http::sequence()
                ->push([
                    'data' => [
                        'orderCancel' => [
                            'job' => [
                                'id' => 'gid://shopify/Job/7188f4d3-b3ab-4980-8b0d-807f0576c4d9',
                                'done' => false,
                            ],
                            'orderCancelUserErrors' => [
                            ],
                        ],
                    ],
                    'extensions' => [
                    ],
                ])
            ->push($jobPending)
            ->push($jobPending)
            ->push($jobPending)
            ->push($jobPending)
            ->push($jobPending)
            ->push($jobPending),
        ]);

        $listener = resolve(InitialAuthFailedListener::class);
        $listener->handle(new InitialAuthFailedEvent($this->order->id));

        $this->assertNotEquals(OrderStatus::CANCELLED, $this->order->refresh()->status);

        Mail::assertNotSent(AuthFailedOrderCancelled::class);
    }
}
