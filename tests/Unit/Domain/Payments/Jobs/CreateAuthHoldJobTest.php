<?php
declare(strict_types=1);

namespace Tests\Unit\Domain\Payments\Jobs;

use App;
use App\Domain\Payments\Jobs\CreateInitialAuthHoldJob;
use App\Domain\Payments\Jobs\ReAuthJob;
use App\Domain\Payments\Services\PaymentService;
use App\Domain\Payments\Values\Order as OrderValue;
use App\Domain\Payments\Values\OrderCreatedEvent;
use App\Domain\Stores\Models\Store;
use App\Domain\Stores\Values\Store as StoreValue;
use Bus;
use Exception;
use Mockery\MockInterface;
use Tests\TestCase;

class CreateAuthHoldJobTest extends TestCase
{
    public function testItCreatesAuthAndSavesTransaction(): void
    {
        Bus::fake(CreateInitialAuthHoldJob::class);

        $this->mock(PaymentService::class, function (MockInterface $mock) {
            $mock->shouldReceive('createInitialAuthHold')->once();
        });

        $currentStore = StoreValue::from(Store::withoutEvents(function () {
            return Store::factory()->create();
        }));
        App::context(store: $currentStore);

        $orderValue = OrderValue::builder()->create(['storeId' => $currentStore->id]);
        $orderCreatedEvent = new OrderCreatedEvent($orderValue);
        $listener = new CreateInitialAuthHoldJob($orderCreatedEvent->order);
        App::call([$listener, 'handle']);

        Bus::assertNotDispatched(CreateInitialAuthHoldJob::class);
    }

    public function testItRedispatchesOnFailure(): void
    {
        Bus::fake(ReAuthJob::class);

        $this->mock(PaymentService::class, function (MockInterface $mock) {
            $mock->shouldReceive('createInitialAuthHold')->times(5)->andThrow(new Exception('An error occurred'));
            $mock->shouldReceive('triggerInitialAuthHoldFailure')->once();
        });

        $currentStore = StoreValue::from(Store::withoutEvents(function () {
            return Store::factory()->create();
        }));
        App::context(store: $currentStore);

        $orderValue = OrderValue::builder()->create(['storeId' => $currentStore->id]);
        $orderCreatedEvent = new OrderCreatedEvent($orderValue);
        $listener = new CreateInitialAuthHoldJob($orderCreatedEvent->order);
        App::call([$listener, 'handle']);

        Bus::assertNotDispatched(ReAuthJob::class);
    }
}
