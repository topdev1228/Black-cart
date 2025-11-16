<?php
declare(strict_types=1);

namespace App\Domain\Billings\Listeners;

use App\Domain\Billings\Services\TbybNetSalesService;
use App\Domain\Billings\Values\OrdersTbybNetSaleCreatedEvent;

class OrdersTbybNetSaleCreatedListener
{
    /**
     * Create the event listener.
     */
    public function __construct(protected TbybNetSalesService $tbybNetSalesService)
    {
    }

    public function handle(OrdersTbybNetSaleCreatedEvent $event): void
    {
        $this->tbybNetSalesService->create($event->tbybNetSale);
    }
}
