<?php
declare(strict_types=1);

namespace App\Domain\Billings\Listeners;

use App\Domain\Billings\Services\SubscriptionService;
use App\Domain\Billings\Values\WebhookShopifyAppSubscriptionsUpdate;

class WebhookShopifyAppSubscriptionsUpdateListener
{
    /**
     * Create the event listener.
     */
    public function __construct(protected SubscriptionService $subscriptionService)
    {
    }

    /**
     * Handle Shopify's app_subscriptions/update event.
     *
     * @see https://shopify.dev/docs/api/admin-rest/2023-10/resources/webhook#event-topics-app-subscriptions-update
     */
    public function handle(WebhookShopifyAppSubscriptionsUpdate $appSubscriptionsUpdate): void
    {
        $this->subscriptionService->updateFromShopifyWebhook($appSubscriptionsUpdate);
    }
}
