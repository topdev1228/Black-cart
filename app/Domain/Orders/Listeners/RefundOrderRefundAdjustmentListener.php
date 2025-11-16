<?php
declare(strict_types=1);

namespace App\Domain\Orders\Listeners;

use App\Domain\Orders\Services\OrderService;
use App\Domain\Orders\Values\OrderCompletedEvent;

class RefundOrderRefundAdjustmentListener
{
    public function __construct(protected OrderService $orderService)
    {
    }

    public function handle(OrderCompletedEvent $event): void
    {
        $this->orderService->refundOrderRefundAdjustments($event->order->id);
    }
}
