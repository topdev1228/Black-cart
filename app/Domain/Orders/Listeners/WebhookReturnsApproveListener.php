<?php
declare(strict_types=1);

namespace App\Domain\Orders\Listeners;

use App\Domain\Orders\Services\ReturnService;
use App\Domain\Orders\Values\OrderReturn as ReturnValue;
use App\Domain\Orders\Values\WebhookReturnsApprove;
use Illuminate\Database\Eloquent\ModelNotFoundException;

class WebhookReturnsApproveListener
{
    public function __construct(
        protected ReturnService $returnService
    ) {
    }

    public function handle(WebhookReturnsApprove $data): ?ReturnValue
    {
        try {
            return $this->returnService->createFromWebhook($data);
        } catch (ModelNotFoundException $e) {
            return null;
        }
    }
}
