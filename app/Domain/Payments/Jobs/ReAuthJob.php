<?php
declare(strict_types=1);

namespace App\Domain\Payments\Jobs;

use App\Domain\Payments\Services\PaymentService;
use App\Domain\Payments\Values\Order as OrderValue;
use App\Domain\Payments\Values\Transaction as TransactionValue;
use App\Domain\Shared\Jobs\BaseJob;
use Illuminate\Foundation\Bus\PendingDispatch;

/**
 * @method static PendingDispatch dispatch(OrderValue $order, TransactionValue $transaction)
 * @method static PendingDispatch dispatchSync(OrderValue $order, TransactionValue $transaction)
 */
class ReAuthJob extends BaseJob
{
    public function __construct(public OrderValue $order, public TransactionValue $transaction)
    {
        parent::__construct();
    }

    public function handle(PaymentService $paymentService): void
    {
        $paymentService->createReAuthHold($this->order);
    }
}
