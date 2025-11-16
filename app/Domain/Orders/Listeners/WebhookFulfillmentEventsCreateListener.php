<?php
declare(strict_types=1);

namespace App\Domain\Orders\Listeners;

use App\Domain\Orders\Services\FulfillmentService;
use App\Domain\Orders\Values\FulfillmentEvent;
use Illuminate\Support\Collection;

class WebhookFulfillmentEventsCreateListener
{
    public function __construct(public FulfillmentService $fulfillmentService)
    {
    }

    public function handle(Collection $data): void
    {
        $this->fulfillmentService->handleFulfillmentEvent(FulfillmentEvent::from($data->toArray()));
    }
}
