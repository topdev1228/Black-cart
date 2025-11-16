<?php
declare(strict_types=1);

namespace App\Domain\Payments\Listeners;

use App\Domain\Payments\Services\PaymentService;
use App\Domain\Payments\Values\PaymentRequiredEvent;
use DateInterval;

class CapturePaymentListener
{
    protected DateInterval $delayInterval;

    public function __construct(protected PaymentService $paymentService)
    {
        $this->delayInterval = config('payments.timings.payment_status_interval');
    }

    /**
     * @see \App\Domain\Orders\Events\PaymentRequiredEvent
     */
    public function handle(PaymentRequiredEvent $event): void
    {
        $this->paymentService->captureOrCreatePayment($event->orderId, $event->sourceOrderId, $event->amount);
    }
}
