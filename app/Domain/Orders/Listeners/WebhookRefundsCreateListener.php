<?php
declare(strict_types=1);

namespace App\Domain\Orders\Listeners;

use App\Domain\Orders\Services\RefundService;
use App\Domain\Orders\Values\WebhookRefundsCreate;

class WebhookRefundsCreateListener
{
    public function __construct(
        protected RefundService $refundsService,
    ) {
    }

    public function handle(WebhookRefundsCreate $data): void
    {
        $this->refundsService->createFromWebhook($data);
    }
}
