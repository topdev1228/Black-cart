<?php
declare(strict_types=1);

namespace App\Domain\Orders\Listeners;

use App\Domain\Orders\Services\OrderService;
use Illuminate\Support\Collection;

class WebhookOrdersCreateListener
{
    public function __construct(
        protected OrderService $orderService,
    ) {
    }

    public function handle(Collection $orderData): void
    {
        $this->orderService->createOrderFromWebhook($orderData);
    }
}
