<?php
declare(strict_types=1);

namespace App\Domain\Shopify\Http\Controllers;

use App;
use App\Domain\Shared\Http\Controllers\Controller;
use App\Domain\Shopify\Services\WebhooksService;
use App\Domain\Shopify\Values\WebhookCustomersDataRequest;
use App\Domain\Shopify\Values\WebhookCustomersRedact;
use App\Domain\Shopify\Values\WebhookShopRedact;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Response;

class WebhooksController extends Controller
{
    public function __construct(protected WebhooksService $webhooksService)
    {
    }

    public function customers_data_request(WebhookCustomersDataRequest $webhookCustomersDataRequest): Response|JsonResponse
    {
        $this->webhooksService->createCustomersDataRequest(App::context()->store->id, $webhookCustomersDataRequest);

        return $this->sendResponse(); // return 200 OK with no body to Shopify to acknowledge receipt
    }

    public function customers_redact(WebhookCustomersRedact $webhookCustomersRedact): Response|JsonResponse
    {
        $webhookCustomersRedact->storeId = App::context()->store->id;

        $this->webhooksService->createCustomersRedact(App::context()->store->id, $webhookCustomersRedact);

        return $this->sendResponse(); // return 200 OK with no body to Shopify to acknowledge receipt
    }

    public function shop_redact(WebhookShopRedact $webhookShopRedact): Response|JsonResponse
    {
        $this->webhooksService->createShopRedact(App::context()->store->id, $webhookShopRedact);

        return $this->sendResponse(); // return 200 OK with no body to Shopify to acknowledge receipt
    }
}
