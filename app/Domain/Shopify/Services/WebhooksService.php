<?php
declare(strict_types=1);

namespace App\Domain\Shopify\Services;

use App\Domain\Shopify\Enums\MandatoryWebhookTopic;
use App\Domain\Shopify\Enums\WebhookTopic;
use App\Domain\Shopify\Repositories\MandatoryWebhookRepository;
use App\Domain\Shopify\Values\MandatoryWebhook as MandatoryWebhookValue;
use App\Domain\Shopify\Values\WebhookCustomersDataRequest;
use App\Domain\Shopify\Values\WebhookCustomersRedact;
use App\Domain\Shopify\Values\WebhookShopRedact;

class WebhooksService
{
    public function __construct(
        protected MandatoryWebhookRepository $mandatoryWebhookRepository,
        protected ShopifyWebhookService $shopifyWebhookService,
    ) {
    }

    public function subscribe(): void
    {
        $this->shopifyWebhookService->subscribe(
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
    }

    public function createCustomersDataRequest(string $storeId, WebhookCustomersDataRequest $webhookCustomersDataRequest): MandatoryWebhookValue
    {
        $mandatoryWebhookValue = new MandatoryWebhookValue(
            $storeId,
            MandatoryWebhookTopic::CUSTOMERS_DATA_REQUEST,
            $webhookCustomersDataRequest->shopId,
            $webhookCustomersDataRequest->shopDomain,
            $webhookCustomersDataRequest->toArray(),
        );

        $newMandatoryWebhookValue = $this->mandatoryWebhookRepository->store($mandatoryWebhookValue);

        // TODO dispatch CustomerDataRequestedEvent

        return $newMandatoryWebhookValue;
    }

    public function createCustomersRedact(string $storeId, WebhookCustomersRedact $webhookCustomersRedact): MandatoryWebhookValue
    {
        $mandatoryWebhookValue = new MandatoryWebhookValue(
            $storeId,
            MandatoryWebhookTopic::CUSTOMERS_REDACT,
            $webhookCustomersRedact->shopId,
            $webhookCustomersRedact->shopDomain,
            $webhookCustomersRedact->toArray(),
        );

        $newMandatoryWebhookValue = $this->mandatoryWebhookRepository->store($mandatoryWebhookValue);

        // TODO dispatch CustomersRedactedEvent

        return $newMandatoryWebhookValue;
    }

    public function createShopRedact(string $storeId, WebhookShopRedact $webhookShopRedact): MandatoryWebhookValue
    {
        $mandatoryWebhookValue = new MandatoryWebhookValue(
            $storeId,
            MandatoryWebhookTopic::SHOP_REDACT,
            $webhookShopRedact->shopId,
            $webhookShopRedact->shopDomain,
            $webhookShopRedact->toArray(),
        );

        $newMandatoryWebhookValue = $this->mandatoryWebhookRepository->store($mandatoryWebhookValue);

        // TODO dispatch ShopRedactedEvent

        return $newMandatoryWebhookValue;
    }
}
