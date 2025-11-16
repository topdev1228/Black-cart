<?php
declare(strict_types=1);

namespace Tests\Feature\Domain\Orders\Listeners;

use App\Domain\Orders\Enums\OrderStatus;
use App\Domain\Orders\Events\LineItemCancelled;
use App\Domain\Orders\Listeners\WebhookOrdersCancelledListener;
use App\Domain\Orders\Models\LineItem;
use App\Domain\Orders\Models\Order;
use App\Domain\Orders\Repositories\OrderRepository;
use App\Domain\Orders\Services\OrderService;
use App\Domain\Stores\Models\Store;
use Event;
use Illuminate\Database\Eloquent\ModelNotFoundException;
use Illuminate\Support\Facades\App;
use Str;
use Tests\TestCase;

class WebhookOrdersCancelledListenerTest extends TestCase
{
    protected $goodData;
    protected $orderService;
    protected $lineItemService;
    protected $shopifyGraphQl;
    protected $trialService;
    protected Store $currentStore;

    protected function setUp(): void
    {
        parent::setUp();

        $this->orderService = resolve(OrderService::class);
        $this->currentStore = Store::withoutEvents(function () {
            Store::factory()->count(5)->create();

            return Store::factory()->create();
        });
        App::context(store: $this->currentStore);
    }

    /**
     * A basic feature test example.
     */
    public function testItCancelsOrderFromWebhookData(): void
    {
        Event::fake();
        $order = Order::factory()->create([
            'source_id' => Str::shopifyGid('5095218610315', 'Order'),
            'store_id' => $this->currentStore->id,
        ]);
        LineItem::factory()->for($order)->create([
            'trialable_id' => (string) Str::uuid(),
        ]);

        $cancelData = collect($this->loadFixtureData('orderCancelled.json', 'Orders'));

        $listener = resolve(WebhookOrdersCancelledListener::class);
        $listener->handle($cancelData);

        $order->refresh();

        $this->assertNotNull($order);
        $this->assertEquals(OrderStatus::CANCELLED, $order->status);
        Event::assertDispatched(LineItemCancelled::class);
    }

    public function testItDoesntCancelNonTrialableItems(): void
    {
        Event::fake();
        $order = Order::factory()->create([
            'source_id' => Str::shopifyGid('5095218610315', 'Order'),
            'store_id' => $this->currentStore->id,
        ]);
        LineItem::factory()->for($order)->create([
            'trialable_id' => null,
        ]);

        $cancelData = collect($this->loadFixtureData('orderCancelled.json', 'Orders'));

        $listener = resolve(WebhookOrdersCancelledListener::class);
        $listener->handle($cancelData);

        $order->refresh();

        $this->assertNotNull($order);
        $this->assertEquals(OrderStatus::CANCELLED, $order->status);
        Event::assertNotDispatched(LineItemCancelled::class);
    }

    public function testItDoesntCancelNonBlackcartOrder(): void
    {
        $orderRepo = $this->mock(OrderRepository::class);
        $orderRepo->shouldReceive('getBySourceId')->andThrow(new ModelNotFoundException('No query results for model [App\Domain\Orders\Models\Order]'));
        $orderRepo->shouldReceive('update')->never();
        $cancelData = collect($this->loadFixtureData('orderCancelled.json', 'Orders'));

        $listener = resolve(WebhookOrdersCancelledListener::class);
        $listener->handle($cancelData);
    }
}
