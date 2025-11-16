<?php

declare(strict_types=1);

namespace App\Domain\Orders\Listeners;

use App\Domain\Orders\Enums\OrderStatus;
use App\Domain\Orders\Mail\AuthFailedOrderCancelled;
use App\Domain\Orders\Services\OrderService;
use App\Domain\Orders\Values\InitialAuthFailedEvent;
use Mail;

class InitialAuthFailedListener
{
    public function __construct(protected OrderService $orderService)
    {
    }

    public function handle(InitialAuthFailedEvent $event): void
    {
        $order = $this->orderService->getById($event->orderId);
        $updatedOrder = $this->orderService->cancelOrder($order, true);
        if ($updatedOrder->status === OrderStatus::CANCELLED) {
            Mail::to($order->customerEmail())->send(new AuthFailedOrderCancelled($order));
        }
    }
}
