<?php
declare(strict_types=1);

namespace Tests\Feature\Domain\Orders\Listeners;

use App\Domain\Orders\Enums\FulfillmentOrderStatus;
use App\Domain\Orders\Listeners\ReleaseFulfillmentListener;
use App\Domain\Orders\Models\Order;
use App\Domain\Orders\Values\CheckoutAuthorizationSuccessEvent;
use App\Domain\Stores\Models\Store;
use App\Domain\Stores\Values\Store as StoreValue;
use Illuminate\Http\Client\Request;
use Illuminate\Support\Facades\App;
use Illuminate\Support\Facades\Date;
use Illuminate\Support\Facades\Http;
use Tests\TestCase;

class ReleaseFulfillmentListenerTest extends TestCase
{
    public function testItReleasesFulfillment(): void
    {
        App::context()->store = StoreValue::from(Store::withoutEvents(function () {
            return Store::factory()->create();
        }));

        Http::fake([
            '*graphql.json' => Http::sequence()
                ->push([
                    'data' => [
                        'order' => [
                            'fulfillmentOrders' => [
                                'nodes' => [
                                    [
                                        'id' => 'testFulfillmentOrderId',
                                        'status' => FulfillmentOrderStatus::CANCELLED->name,
                                        'createdAt' => Date::now()->toIso8601String(),
                                    ],
                                    [
                                        'id' => 'testFulfillmentOrderId2',
                                        'status' => FulfillmentOrderStatus::ON_HOLD->name,
                                        'createdAt' => Date::now()->toIso8601String(),
                                    ],
                                    [
                                        'id' => 'testFulfillmentOrderId3',
                                        'status' => FulfillmentOrderStatus::IN_PROGRESS->name,
                                        'createdAt' => Date::now()->toIso8601String(),
                                    ],
                                ],
                            ],
                        ],
                    ],
                ], 200)
                ->push([
                    'data' => [
                        'fulfillmentOrderReleaseHold' => [
                            'fulfillmentOrder' => [
                                'id' => 'testFulfillmentOrderId', 'status' => FulfillmentOrderStatus::IN_PROGRESS,
                            ],
                        ],
                    ],
                ], 200),
        ]);

        $order = Order::factory()->create([
            'store_id' => App::context()->store->id,
            'source_id' => 'testOrderId',
        ]);

        $event = CheckoutAuthorizationSuccessEvent::builder()->create(['order_id' => $order->id, 'source_id' => $order->source_id]);

        $releaseFulfillmentListener = resolve(ReleaseFulfillmentListener::class);
        $releaseFulfillmentListener->handle($event);

        Http::assertSentCount(2);
        Http::assertSent(function (Request $request) {
            return $request->method() === 'POST' &&
                $request->hasHeader('X-Shopify-Access-Token', App::context()->store->accessToken);
        });
    }
}
