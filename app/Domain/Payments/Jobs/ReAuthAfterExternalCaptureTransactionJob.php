<?php
declare(strict_types=1);

namespace App\Domain\Payments\Jobs;

use App\Domain\Payments\Services\PaymentService;
use App\Domain\Shared\Jobs\BaseJob;
use Illuminate\Foundation\Bus\PendingDispatch;

/**
 * @method static PendingDispatch dispatch(string $orderId)
 * @method static PendingDispatch dispatchSync(string $orderId)
 */
class ReAuthAfterExternalCaptureTransactionJob extends BaseJob
{
    public function __construct(public string $orderId)
    {
        parent::__construct();
    }

    public function handle(PaymentService $paymentService): void
    {
        $paymentService->createReAuthHoldNoCaptureOnFailure($this->orderId);
    }
}
