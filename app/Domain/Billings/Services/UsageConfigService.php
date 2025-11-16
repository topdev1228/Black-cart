<?php
declare(strict_types=1);

namespace App\Domain\Billings\Services;

use App\Domain\Billings\Enums\SubscriptionLineItemType;
use App\Domain\Billings\Enums\SubscriptionStatus;
use App\Domain\Billings\Repositories\UsageConfigRepository;
use App\Domain\Billings\Values\Subscription as SubscriptionValue;
use App\Domain\Billings\Values\SubscriptionLineItem;
use App\Domain\Billings\Values\UsageConfig;

class UsageConfigService
{
    public function __construct(
        protected UsageConfigRepository $repository,
        protected SubscriptionService $subscriptionService,
    ) {
    }

    public function create(UsageConfig $config): UsageConfig
    {
        return $this->repository->create($config);
    }

    public function getLatestConfig(): UsageConfig
    {
        return $this->repository->get();
    }

    public function createForSubscription(SubscriptionValue $subscriptionInput): ?UsageConfig
    {
        if (empty($subscriptionInput->shopifyAppSubscriptionId)) {
            return null;
        }

        // This call is necessary to get the subscription line items as well, in case they are not already loaded
        $subscription = $this->subscriptionService->getByShopifyAppSubscriptionId($subscriptionInput->shopifyAppSubscriptionId);

        if ($subscription->status !== SubscriptionStatus::ACTIVE) {
            return null;
        }

        if (empty($subscription->subscriptionLineItems)) {
            return null;
        }

        foreach ($subscription->subscriptionLineItems as $subscriptionLineItem) {
            if ($subscriptionLineItem->type === SubscriptionLineItemType::USAGE) {
                /**
                 * @var SubscriptionLineItem $lineItem
                 */
                $lineItem = $subscriptionLineItem;
                break;
            }
        }

        if (empty($lineItem)) {
            return null;
        }

        return $this->create(UsageConfig::from([
            'store_id' => $subscription->storeId,
            'subscription_line_item_id' => $lineItem->shopifyAppSubscriptionLineItemId,
            'description' => '',
            'config' => [
                [
                    'start' => 0,
                    'end' => 250000,
                    'step' => 250000,
                    'price' => 0,
                    'currency' => $lineItem->usageCappedAmountCurrency,
                ],
                [
                    'start' => 250001,
                    'end' => null,
                    'step' => 250000,
                    'price' => 10000,
                    'currency' => $lineItem->usageCappedAmountCurrency,
                ],
            ],
            'currency' => $lineItem->usageCappedAmountCurrency,
            'validFrom' => now(),
            'validTo' => null,
        ]));
    }
}
