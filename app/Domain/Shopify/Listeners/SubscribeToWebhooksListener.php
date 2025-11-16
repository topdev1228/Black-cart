<?php
declare(strict_types=1);

namespace App\Domain\Shopify\Listeners;

use App\Domain\Shopify\Services\WebhooksService;

class SubscribeToWebhooksListener
{
    public function __construct(protected WebhooksService $webhooksService)
    {
    }

    public function handle(): void
    {
        $this->webhooksService->subscribe();
    }
}
