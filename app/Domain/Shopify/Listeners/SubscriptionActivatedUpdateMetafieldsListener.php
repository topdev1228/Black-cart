<?php
declare(strict_types=1);

namespace App\Domain\Shopify\Listeners;

use App\Domain\Shopify\Services\MetafieldsService;
use App\Domain\Shopify\Values\SubscriptionActivatedEvent;

class SubscriptionActivatedUpdateMetafieldsListener
{
    public function __construct(protected MetafieldsService $metafieldsService)
    {
    }

    public function handle(SubscriptionActivatedEvent $event): void
    {
        $this->metafieldsService->upsertSubscriptionStatusMetafield($event->subscription);
    }
}
