<?php
declare(strict_types=1);

namespace Tests\Unit\Domain\Orders\Listeners;

use App\Domain\Orders\Events\PaymentRequiredEvent;
use App\Domain\Orders\Listeners\TrialGroupExpiredListener;
use App\Domain\Orders\Services\OrderService;
use App\Domain\Orders\Values\Order as OrderValue;
use App\Domain\Orders\Values\TrialGroupExpiredEvent as TrialGroupExpiredEventValue;
use Illuminate\Support\Facades\Event;
use Mockery\MockInterface;
use Tests\TestCase;

class TrialGroupExpiredListenerTest extends TestCase
{
    public function testItHandlesTrialGroupExpiredEvent(): void
    {
        Event::fake();

        $groupKey = 'test-group-key';
        $orderId = 'test-order-id';
        $sourceId = 'test-source-id';

        $orderValue = OrderValue::builder()->create([
            'id' => $orderId,
            'source_id' => $sourceId,
        ]);

        $this->mock(OrderService::class, function (MockInterface $mock) use ($orderValue, $groupKey) {
            $mock->shouldReceive('getByTrialGroupId')->once()->with($groupKey)->andReturn($orderValue);
        });

        $trialGroupExpiredListener = resolve(TrialGroupExpiredListener::class);

        $trialGroupExpiredListener->handle(new TrialGroupExpiredEventValue($groupKey));

        Event::assertDispatched(PaymentRequiredEvent::class, function (PaymentRequiredEvent $event) use ($orderId, $sourceId, $groupKey) {
            $this->assertEquals($event->orderId, $orderId);
            $this->assertEquals($event->sourceOrderId, $sourceId);
            $this->assertEquals($event->trialGroupId, $groupKey);

            return true;
        });
    }
}
