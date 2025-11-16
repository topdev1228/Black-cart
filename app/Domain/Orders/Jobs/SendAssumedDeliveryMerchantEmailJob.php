<?php

declare(strict_types=1);

namespace App\Domain\Orders\Jobs;

use App\Domain\Orders\Services\OrderService;
use App\Domain\Orders\Values\Order as OrderValue;
use App\Domain\Shared\Jobs\BaseJob;
use Illuminate\Foundation\Bus\PendingDispatch;

/**
 * @method static PendingDispatch dispatch(OrderValue $order)
 * @method static PendingDispatch dispatchSync(OrderValue $order)
 */
class SendAssumedDeliveryMerchantEmailJob extends BaseJob
{
    public function __construct(protected OrderValue $order)
    {
        parent::__construct();
    }

    public function handle(OrderService $orderService): void
    {
        $orderService->sendAssumedDeliveryMerchantNotification($this->order->id);
    }
}
