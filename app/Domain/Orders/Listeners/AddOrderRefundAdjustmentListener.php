<?php
declare(strict_types=1);

namespace App\Domain\Orders\Listeners;

use App\Domain\Orders\Services\OrderService;
use App\Domain\Orders\Values\RefundCreatedEvent;

class AddOrderRefundAdjustmentListener
{
    public function __construct(protected OrderService $orderService)
    {
    }

    public function handle(RefundCreatedEvent $event): void
    {
        $this->orderService->addOrderRefundAdjustment($event->refund->orderId, $event->refund->refundedCustomerAmount);
    }
}
