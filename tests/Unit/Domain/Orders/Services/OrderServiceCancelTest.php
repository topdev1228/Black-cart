<?php
declare(strict_types=1);

namespace Tests\Unit\Domain\Orders\Services;

use App\Domain\Orders\Enums\OrderStatus;
use App\Domain\Orders\Exceptions\ShopifyOrderCancellationFailedException;
use App\Domain\Orders\Exceptions\ShopifyOrderCancellationPendingException;
use App\Domain\Orders\Repositories\OrderRepository;
use App\Domain\Orders\Services\OrderService;
use App\Domain\Orders\Services\ShopifyOrderService;
use App\Domain\Orders\Values\Order;
use App\Domain\Orders\Values\Order as OrderValue;
use App\Domain\Stores\Models\Store;
use App\Domain\Stores\Values\Store as StoreValue;
use Illuminate\Support\Facades\App;
use Tests\TestCase;

class OrderServiceCancelTest extends TestCase
{
    protected StoreValue $store;
    protected OrderValue $order;

    protected OrderService $orderService;
    protected ShopifyOrderService $shopifyOrderService;
    protected OrderRepository $orderRepository;

    protected function setUp(): void
    {
        parent::setUp();

        $this->store = StoreValue::from(Store::withoutEvents(function () {
            return Store::factory()->create();
        }));

        App::context(store: $this->store);
        $this->order = Order::builder()->create([
            'store_id' => $this->store->id,
        ]);

        $this->shopifyOrderService = $this->mock(ShopifyOrderService::class);
        $this->orderRepository = $this->mock(OrderRepository::class);

        $this->orderService = resolve(OrderService::class);
    }

    public function testItCancelsOrderLocally(): void
    {
        $this->orderRepository->shouldReceive('update')->once();
        $this->shopifyOrderService->shouldReceive('cancelOrder')->never();

        $this->orderService->cancelOrder($this->order, false);
    }

    public function testItCancelsOrderLocallyOnRemoteSuccess(): void
    {
        $this->orderRepository->shouldReceive('update')->once()->withArgs(function ($orderValue) {
            return OrderStatus::CANCELLED === $orderValue->status;
        });
        $this->shopifyOrderService->shouldReceive('cancelOrder')->once();

        $this->orderService->cancelOrder($this->order, true);
    }

    public function testItDoesntCancelOrderOnRemoteUserError(): void
    {
        $this->orderRepository->shouldReceive('update')->never();
        $this->shopifyOrderService->shouldReceive('cancelOrder')->once()->andThrow(new ShopifyOrderCancellationFailedException());

        $this->orderService->cancelOrder($this->order, true);
    }

    public function testItDoesntCancelOrderOnRemoteJobPending(): void
    {
        $this->orderRepository->shouldReceive('update')->never();
        $this->shopifyOrderService->shouldReceive('cancelOrder')->once()->andThrow(new ShopifyOrderCancellationPendingException());

        $this->orderService->cancelOrder($this->order, true);
    }
}
