<?php

declare(strict_types=1);

namespace App\Domain\Orders\Jobs;

use App\Domain\Orders\Services\OrderService;
use App\Domain\Orders\Values\Order as OrderValue;
use App\Domain\Shared\Jobs\BaseJob;

class AssumeOrderDeliveredJob extends BaseJob
{
    public function __construct(protected OrderValue $order)
    {
        parent::__construct();
    }

    public function handle(OrderService $orderService): void
    {
        $orderService->assumeDelivered($this->order->id);
    }
}
