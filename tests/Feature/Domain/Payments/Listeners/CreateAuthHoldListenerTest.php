<?php
declare(strict_types=1);

namespace Tests\Feature\Domain\Payments\Listeners;

use App;
use App\Domain\Payments\Jobs\CreateInitialAuthHoldJob;
use App\Domain\Payments\Listeners\CreateAuthHoldListener;
use App\Domain\Payments\Services\PaymentService;
use App\Domain\Payments\Values\Order as OrderValue;
use App\Domain\Payments\Values\OrderCreatedEvent;
use App\Domain\Stores\Models\Store;
use App\Domain\Stores\Values\Store as StoreValue;
use Bus;
use Tests\TestCase;

class CreateAuthHoldListenerTest extends TestCase
{
    public function testItDispatchesJob(): void
    {
        $paymentService = resolve(PaymentService::class);
        $currentStore = StoreValue::from(Store::withoutEvents(function () {
            Store::factory()->count(5)->create();

            return Store::factory()->create();
        }));
        App::context(store: $currentStore);

        Bus::fake(CreateInitialAuthHoldJob::class);

        $orderValue = OrderValue::builder()->create(['storeId' => $currentStore->id]);
        $orderCreatedEvent = new OrderCreatedEvent($orderValue);
        $listener = new CreateAuthHoldListener($paymentService);
        $listener->handle($orderCreatedEvent);

        Bus::assertDispatched(CreateInitialAuthHoldJob::class);
    }
}
