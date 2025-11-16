<?php

declare(strict_types=1);

namespace Tests\Unit\Domain\Shopify\Listeners;

use App\Domain\Shopify\Enums\WebhookTopic;
use App\Domain\Shopify\Listeners\SubscribeToWebhooksListener;
use App\Domain\Shopify\Services\ShopifyWebhookService;
use App\Domain\Shopify\Services\WebhooksService;
use Tests\TestCase;

class SubscribeToWebhooksListenerTest extends TestCase
{
    public function testHandle(): void
    {
        $shopifyWebhookService = $this->mock(ShopifyWebhookService::class);
        $shopifyWebhookService
            ->shouldReceive('subscribe')
            ->once()
            ->with(
                WebhookTopic::APP_SUBSCRIPTIONS_UPDATE,
                WebhookTopic::APP_UNINSTALLED,
                WebhookTopic::BULK_OPERATIONS_FINISH,
                WebhookTopic::FULFILLMENT_EVENTS_CREATE,
                WebhookTopic::ORDER_TRANSACTIONS_CREATE,
                WebhookTopic::ORDERS_CANCELLED,
                WebhookTopic::ORDERS_CREATE,
                WebhookTopic::PAYMENT_SCHEDULES_DUE,
                WebhookTopic::REFUNDS_CREATE,
                WebhookTopic::RETURNS_APPROVE,
            );

        $listener = new SubscribeToWebhooksListener(resolve(WebhooksService::class));
        $listener->handle();
    }
}
