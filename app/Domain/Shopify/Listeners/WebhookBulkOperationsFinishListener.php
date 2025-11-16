<?php
declare(strict_types=1);

namespace App\Domain\Shopify\Listeners;

use App\Domain\Shopify\Services\JobsService;
use App\Domain\Shopify\Values\WebhookBulkOperationsFinish as WebhookBulkOperationsFinishValue;
use App\Exceptions\NotImplementedException;

class WebhookBulkOperationsFinishListener
{
    /**
     * Create the event listener.
     */
    public function __construct(protected JobsService $jobsService)
    {
    }

    /**
     * Handle Shopify's app_subscriptions/update event.
     *
     * @see https://shopify.dev/docs/api/admin-rest/2023-10/resources/webhook#event-topics-app-subscriptions-update
     */
    public function handle(WebhookBulkOperationsFinishValue $webhookBulkOperationsFinishValue): void
    {
        //        The bulk_operations/finish webhook payload:
        //        $message->data = {
        //            {
        //              "admin_graphql_api_id": "gid://shopify/BulkOperation/147595010",
        //              "completed_at": "2024-01-09T05:54:12-05:00",
        //              "created_at": "2024-01-09T05:54:12-05:00",
        //              "error_code": null,
        //              "status": "completed",
        //              "type": "query"
        //            }
        //        }

        try {
            $this->jobsService->updateOnBulkOperationsFinish($webhookBulkOperationsFinishValue);
        } catch (NotImplementedException $e) {
            report($e);
        }
    }
}
