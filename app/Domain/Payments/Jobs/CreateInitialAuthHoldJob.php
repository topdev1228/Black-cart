<?php
declare(strict_types=1);

namespace App\Domain\Payments\Jobs;

use App\Domain\Payments\Services\PaymentService;
use App\Domain\Payments\Values\Order;
use App\Domain\Shared\Jobs\BaseJob;
use Exception;
use Illuminate\Foundation\Bus\PendingDispatch;
use Illuminate\Support\Facades\App;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Sleep;

/**
 * @method static PendingDispatch dispatch(Order $order)
 * @method static PendingDispatch dispatchSync(Order $order)
 */
class CreateInitialAuthHoldJob extends BaseJob
{
    const MAX_ATTEMPTS = 5;

    public function __construct(protected Order $order)
    {
        parent::__construct();
    }

    public function handle(PaymentService $paymentService): void
    {
        $attempts = 0;

        while ($attempts < static::MAX_ATTEMPTS) {
            try {
                $paymentService->createInitialAuthHold($this->order);

                return;
            } catch (Exception $e) {
                $attempts++;

                Log::error('Initial auth error: ' . $e->getMessage(), [
                    'order_id' => $this->order->id,
                    'source_id' => $this->order->sourceId,
                    'store_id' => App::context()->store->domain,
                    'attempts' => $attempts,
                ]);

                // Cloud Tasks' time limit for response is 10 minutes.
                // The formula is $attempts x 30 seconds.  Over 5 attempts, we would sleep for a total of 7.5 minutes.
                // This should be sufficient for any glitches to work itself out before we cancel the order.
                Sleep::for($attempts * 30)->seconds();
            }
        }

        $paymentService->triggerInitialAuthHoldFailure($this->order->id);
    }
}
