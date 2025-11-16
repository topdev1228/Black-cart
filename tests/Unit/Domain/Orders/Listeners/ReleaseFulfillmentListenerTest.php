<?php
declare(strict_types=1);

namespace Tests\Unit\Domain\Orders\Listeners;

use App\Domain\Orders\Listeners\ReleaseFulfillmentListener;
use App\Domain\Orders\Services\OrderService;
use App\Domain\Orders\Values\CheckoutAuthorizationSuccessEvent;
use Mockery\MockInterface;
use Tests\TestCase;

class ReleaseFulfillmentListenerTest extends TestCase
{
    public function testItListens(): void
    {
        $this->mock(OrderService::class, function (MockInterface $mock) {
            $mock->shouldReceive('releaseFulfillment')->once();
        });

        $event = CheckoutAuthorizationSuccessEvent::builder()->create();

        $releaseFulfillmentListener = resolve(ReleaseFulfillmentListener::class);
        $releaseFulfillmentListener->handle($event);
    }
}
