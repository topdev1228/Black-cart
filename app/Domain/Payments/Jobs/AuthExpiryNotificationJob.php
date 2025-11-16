<?php
declare(strict_types=1);

namespace App\Domain\Payments\Jobs;

use App\Domain\Payments\Services\PaymentService;
use App\Domain\Payments\Values\Order as OrderValue;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Foundation\Bus\PendingDispatch;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;

/**
 * @method static PendingDispatch dispatch(OrderValue $order, string $transactionSourceId)
 * @method static PendingDispatch dispatchSync(OrderValue $order, string $transactionSourceId)
 */
class AuthExpiryNotificationJob implements ShouldQueue
{
    use Dispatchable;
    use InteractsWithQueue;
    use Queueable;
    use SerializesModels;

    /**
     * Create a new job instance.
     */
    public function __construct(protected OrderValue $order, protected string $transactionSourceId)
    {
    }

    /**
     * Execute the job.
     */
    public function handle(PaymentService $paymentService): void
    {
        $paymentService->sendAuthExpiryNotification($this->order, $this->transactionSourceId);
    }
}
