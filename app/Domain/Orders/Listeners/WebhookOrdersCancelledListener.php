<?php
declare(strict_types=1);

namespace App\Domain\Orders\Listeners;

use App\Domain\Orders\Services\LineItemService;
use App\Domain\Orders\Services\OrderService;
use Illuminate\Database\Eloquent\ModelNotFoundException;
use Illuminate\Support\Collection;

class WebhookOrdersCancelledListener
{
    public function __construct(
        protected OrderService $orderService,
        protected LineItemService $lineItemService
    ) {
    }

    public function handle(Collection $orderData): void
    {
        $orderId = (string) $orderData['id'];

        try {
            $order = $this->orderService->getBySourceId($orderId);
        } catch (ModelNotFoundException) {
            return;
        }

        $this->orderService->cancelOrder($order);
        $this->lineItemService->cancelForOrder($order);
    }
}
