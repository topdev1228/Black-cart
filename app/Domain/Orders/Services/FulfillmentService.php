<?php
declare(strict_types=1);

namespace App\Domain\Orders\Services;

use App\Domain\Orders\Enums\FulfillmentEventStatus;
use App\Domain\Orders\Enums\LineItemStatus;
use App\Domain\Orders\Events\TrialableDeliveredEvent;
use App\Domain\Orders\Repositories\OrderRepository;
use App\Domain\Orders\Values\FulfillmentEvent;
use App\Domain\Orders\Values\LineItem;
use App\Domain\Shared\Services\ShopifyGraphqlService;
use Illuminate\Database\Eloquent\ModelNotFoundException;

class FulfillmentService
{
    public function __construct(
        protected OrderRepository $orderRepository,
        protected LineItemService $lineItemService,
        protected ShopifyGraphqlService $shopifyGraphqlService,
    ) {
    }

    public function handleFulfillmentEvent(FulfillmentEvent $event): void
    {
        try {
            $this->orderRepository->getBySourceId((string) $event->orderId);
        } catch (ModelNotFoundException) {
            return;
        }

        $lineItems = $this->getFulfilledLineItems($event->fulfillmentId);

        foreach ($lineItems as $lineItem) {
            $lineItemStatusUpdated = false;

            if ($event->status === FulfillmentEventStatus::DELIVERED) {
                $lineItem->status = LineItemStatus::DELIVERED;
                $lineItemStatusUpdated = true;
            } elseif (in_array($event->status, [FulfillmentEventStatus::ATTEMPTED_DELIVERY, FulfillmentEventStatus::IN_TRANSIT, FulfillmentEventStatus::OUT_FOR_DELIVERY, FulfillmentEventStatus::READY_FOR_PICKUP])) {
                $lineItem->status = LineItemStatus::FULFILLED;
                $lineItemStatusUpdated = true;
            }

            if ($lineItemStatusUpdated) {
                $this->lineItemService->save($lineItem);

                if ($event->status === FulfillmentEventStatus::DELIVERED) {
                    TrialableDeliveredEvent::dispatch($lineItem->id, TrialService::TRIAL_SOURCE_KEY);
                }
            }
        }
    }

    /**
     * @return LineItem[]
     */
    public function getFulfilledLineItems(int $fulfillmentId): array
    {
        $query = <<<QUERY
        query {
            fulfillment(id: "gid://shopify/Fulfillment/$fulfillmentId") {
              id
              displayStatus
              fulfillmentLineItems(first: 20) {
                edges {
                  node {
                    id
                    lineItem {
                      id
                      quantity
                    }
                  }
                }
              }
            }
        }
        QUERY;

        $graphFulfillment = $this->shopifyGraphqlService->post($query)['data']['fulfillment'];
        $lineItems = [];

        foreach ($graphFulfillment['fulfillmentLineItems']['edges'] as $graphLineItem) {
            $lineItemId = $graphLineItem['node']['lineItem']['id'];
            try {
                $lineItem = $this->lineItemService->getBysourceId($lineItemId);
            } catch (ModelNotFoundException $e) {
                continue;
            }
            $lineItems[] = $lineItem;
        }

        return $lineItems;
    }
}
