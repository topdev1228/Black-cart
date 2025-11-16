<?php
declare(strict_types=1);

namespace Tests\Unit\Domain\Orders\Services;

use App;
use App\Domain\Orders\Enums\FulfillmentEventStatus;
use App\Domain\Orders\Enums\LineItemStatus;
use App\Domain\Orders\Events\TrialableDeliveredEvent;
use App\Domain\Orders\Models\LineItem;
use App\Domain\Orders\Models\Order;
use App\Domain\Orders\Repositories\OrderRepository;
use App\Domain\Orders\Services\FulfillmentService;
use App\Domain\Orders\Services\LineItemService;
use App\Domain\Orders\Values\FulfillmentEvent;
use App\Domain\Orders\Values\Order as OrderValue;
use App\Domain\Shared\Services\ShopifyGraphqlService;
use App\Domain\Stores\Models\Store;
use Event;
use Illuminate\Database\Eloquent\Factories\Sequence;
use Illuminate\Database\Eloquent\ModelNotFoundException;
use Mockery\MockInterface;
use Tests\TestCase;

class FulfillmentServiceTest extends TestCase
{
    protected $orderServiceMock;
    protected $graphQlServiceMock;
    protected $fulfillmentService;
    protected Store $store;

    protected function setUp(): void
    {
        parent::setUp();

        $this->orderServiceMock = $this->mock(OrderRepository::class);
        $this->graphQlServiceMock = $this->mock(ShopifyGraphqlService::class);
        $this->fulfillmentService = resolve(FulfillmentService::class);
        $this->store = Store::factory()->create();

        App::context(store: $this->store);
        Event::fake([TrialableDeliveredEvent::class]);
    }

    public function testItDispatchesDeliveryEvent(): void
    {
        $order = Order::factory()->create([
            'store_id' => $this->store->id,
            'source_id' => 'gid://shopify/Order/5095450214539',
        ]);
        $lineItems = LineItem::factory()->count(2)->state(new Sequence(
            [
                'source_id' => 'gid://shopify/LineItem/12878423851147',
                'trialable_id' => '12345',
                'trial_group_id' => '67890',
            ],
            [
                'source_id' => 'gid://shopify/LineItem/12878423883915',
                'trialable_id' => null,
                'trial_group_id' => null,
            ],
        ))
            ->for($order)
            ->create([]);

        $event = FulfillmentEvent::builder()->create([
            'status' => FulfillmentEventStatus::DELIVERED,
        ]);

        $graphResponse = $this->loadFixtureData('fulfillmentEvent.json', 'Orders');

        $this->graphQlServiceMock->shouldReceive('post')
            ->once()
            ->andReturn($graphResponse);

        $this->orderServiceMock->shouldReceive('getBySourceId')
            ->andReturn(OrderValue::from(Order::find($order->id)));

        $this->fulfillmentService->handleFulfillmentEvent($event);

        foreach ($order->refresh()->lineItems as $lineItem) {
            $this->assertEquals(LineItemStatus::DELIVERED, $lineItem->status);
        }

        Event::assertDispatched(TrialableDeliveredEvent::class, function (TrialableDeliveredEvent $event) use ($lineItems) {
            $this->assertTrue(in_array($event->trialable->sourceId, $lineItems->pluck('id')->all()));

            return true;
        });

        Event::assertDispatchedTimes(TrialableDeliveredEvent::class, 2);
    }

    public function testItDoesNotUpdateLineItemStatusIfUnchanged(): void
    {
        $order = Order::factory()->create([
            'store_id' => $this->store->id,
            'source_id' => 'gid://shopify/Order/5095450214539',
        ]);
        LineItem::factory()->count(2)->state(new Sequence(
            [
                'source_id' => 'gid://shopify/LineItem/12878423851147',
                'trialable_id' => '12345',
                'trial_group_id' => '67890',
                'status' => LineItemStatus::FULFILLED,
            ],
            [
                'source_id' => 'gid://shopify/LineItem/12878423883915',
                'trialable_id' => null,
                'trial_group_id' => null,
                'status' => LineItemStatus::FULFILLED,
            ],
        ))
            ->for($order)
            ->create([]);

        $event = FulfillmentEvent::builder()->create([
            'status' => FulfillmentEventStatus::CONFIRMED,
        ]);

        $graphResponse = $this->loadFixtureData('fulfillmentEvent.json', 'Orders');

        $this->graphQlServiceMock->shouldReceive('post')
            ->once()
            ->andReturn($graphResponse);

        $this->orderServiceMock->shouldReceive('getBySourceId')
            ->andReturn(OrderValue::from(Order::find($order->id)));

        $this->partialMock(LineItemService::class, function (MockInterface $mock) {
            $mock->shouldNotReceive('update');
        });

        $this->fulfillmentService->handleFulfillmentEvent($event);

        foreach ($order->refresh()->lineItems as $lineItem) {
            $this->assertEquals(LineItemStatus::FULFILLED, $lineItem->status);
        }

        Event::assertNotDispatched(TrialableDeliveredEvent::class);
    }

    public function testItIgnoresNonBlackcartOrders(): void
    {
        $nonDeliveredEvent = FulfillmentEvent::builder()->create([
            'status' => FulfillmentEventStatus::DELIVERED,
        ]);

        $this->orderServiceMock
            ->shouldReceive('getBySourceId')
            ->once()
            ->andThrow(new ModelNotFoundException());
        $this->graphQlServiceMock->shouldNotReceive('post');

        $this->fulfillmentService->handleFulfillmentEvent($nonDeliveredEvent);
    }

    public function testItUpdatesLineItemStatusToFulfilled(): void
    {
        $statuses = [
            FulfillmentEventStatus::ATTEMPTED_DELIVERY,
            FulfillmentEventStatus::IN_TRANSIT,
            FulfillmentEventStatus::OUT_FOR_DELIVERY,
            FulfillmentEventStatus::READY_FOR_PICKUP,
        ];

        $order = Order::factory()->create([
            'store_id' => $this->store->id,
            'source_id' => 'gid://shopify/Order/5095450214539',
        ]);
        LineItem::factory()->count(2)->state(new Sequence(
            [
                'source_id' => 'gid://shopify/LineItem/12878423851147',
                'trialable_id' => '12345',
                'trial_group_id' => '67890',
            ],
            [
                'source_id' => 'gid://shopify/LineItem/12878423883915',
                'trialable_id' => null,
                'trial_group_id' => null,
            ],
        ))
            ->for($order)
            ->create([]);

        foreach ($statuses as $status) {
            $event = FulfillmentEvent::builder()->create(['status' => $status]);
            $graphResponse = $this->loadFixtureData('fulfillmentEvent.json', 'Orders');

            $this->graphQlServiceMock->shouldReceive('post')
                ->once()
                ->andReturn($graphResponse);
            $this->orderServiceMock->shouldReceive('getBySourceId')
                ->andReturn(OrderValue::from(Order::find($order->id)));
            $this->fulfillmentService->handleFulfillmentEvent($event);

            foreach ($order->refresh()->lineItems as $lineItem) {
                $this->assertEquals(LineItemStatus::FULFILLED, $lineItem->status);
            }

            Event::assertNotDispatched(TrialableDeliveredEvent::class);
        }
    }
}
