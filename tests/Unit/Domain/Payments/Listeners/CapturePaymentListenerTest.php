<?php
declare(strict_types=1);

namespace Tests\Unit\Domain\Payments\Listeners;

use App\Domain\Orders\Events\PaymentRequiredEvent;
use App\Domain\Payments\Listeners\CapturePaymentListener;
use App\Domain\Payments\Services\PaymentService;
use App\Domain\Payments\Values\PaymentRequiredEvent as PaymentRequiredEventValue;
use App\Domain\Payments\Values\Transaction;
use App\Domain\Shared\Services\ShopifyGraphqlService;
use Brick\Money\Money;
use Tests\TestCase;

class CapturePaymentListenerTest extends TestCase
{
    public function testItHandlesPaymentRequiredEvent(): void
    {
        $this->mock(ShopifyGraphqlService::class);
        $orderPaymentService = $this->mock(PaymentService::class);

        $sourceOrderId = 'test-source-order-id';
        $orderId = 'test-order-id';
        $trialGroupId = 'test-trial-group-id';

        $orderPaymentService->shouldReceive('captureOrCreatePayment')->once()->withArgs(function (string $inputOrderId, string $inputSourceOrderId, Money $money) use ($orderId, $sourceOrderId) {
            $this->assertEquals($orderId, $inputOrderId);
            $this->assertEquals($sourceOrderId, $inputSourceOrderId);
            $this->assertTrue($money->isEqualTo(Money::ofMinor(100, 'USD')));

            return true;
        })->andReturn(Transaction::builder()->create());

        $listener = resolve(CapturePaymentListener::class);
        $listener->handle(PaymentRequiredEventValue::from(
            (new PaymentRequiredEvent($orderId, $sourceOrderId, $trialGroupId, Money::ofMinor(100, 'USD')))->broadcastWith()
        ));
    }
}
