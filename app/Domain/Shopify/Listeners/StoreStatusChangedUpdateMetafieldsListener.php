<?php
declare(strict_types=1);

namespace App\Domain\Shopify\Listeners;

use App\Domain\Shopify\Enums\StoreStatus;
use App\Domain\Shopify\Services\MetafieldsService;
use App\Domain\Shopify\Services\WebhooksService;
use App\Domain\Shopify\Values\StoreStatusChangedEvent;

class StoreStatusChangedUpdateMetafieldsListener
{
    public function __construct(
        protected MetafieldsService $metafieldsService,
        protected WebhooksService $webhookService,
    ) {
    }

    public function handle(StoreStatusChangedEvent $event): void
    {
        $this->metafieldsService->upsertStoreStatusMetefields($event->status);

        if ($event->status === StoreStatus::ACTIVE) {
            $this->webhookService->subscribe();
        }
    }
}
