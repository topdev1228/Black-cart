<?php
declare(strict_types=1);

namespace App\Domain\Payments\Jobs;

use App\Domain\Payments\Exceptions\PaymentFailedException;
use App\Domain\Payments\Services\PaymentService;
use App\Domain\Shared\Jobs\BaseJob;
use DateInterval;
use Illuminate\Foundation\Bus\PendingDispatch;

/**
 * @method static PendingDispatch dispatch(string $jobId, string $paymentReferenceId, string $orderId)
 * @method static PendingDispatch dispatchSync(string $jobId, string $paymentReferenceId, string $orderId)
 *
 * @psalm-suppress MethodSignatureMismatch
 * @psalm-suppress MoreSpecificImplementedParamType
 */
class PaymentAttemptJob extends BaseJob
{
    protected DateInterval $delayInterval;

    public function __construct(protected string $jobId, protected string $paymentReferenceId, protected string $orderId)
    {
        parent::__construct();

        $this->delayInterval = config('payments.timings.payment_status_interval');
    }

    public function handle(PaymentService $orderPaymentService): void
    {
        try {
            if (!$orderPaymentService->verifyPayment($this->jobId, $this->paymentReferenceId, $this->orderId)) {
                static::dispatch($this->jobId, $this->paymentReferenceId, $this->orderId)->delay($this->delayInterval);
            }
        } catch (PaymentFailedException) {
            // Avoid job failing
        }
    }
}
