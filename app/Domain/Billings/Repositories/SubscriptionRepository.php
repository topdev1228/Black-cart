<?php
declare(strict_types=1);

namespace App\Domain\Billings\Repositories;

use App\Domain\Billings\Enums\SubscriptionStatus;
use App\Domain\Billings\Models\Subscription;
use App\Domain\Billings\Values\Collections\SubscriptionCollection;
use App\Domain\Billings\Values\Subscription as SubscriptionValue;
use InvalidArgumentException;

class SubscriptionRepository
{
    public function store(SubscriptionValue $subscriptionValue): SubscriptionValue
    {
        return SubscriptionValue::from(Subscription::create($subscriptionValue->toArray()));
    }

    public function getByShopifyAppSubscriptionId(string $shopifyAppSubscriptionId): SubscriptionValue
    {
        return SubscriptionValue::from(
            Subscription::where('shopify_app_subscription_id', $shopifyAppSubscriptionId)->firstOrFail()
        );
    }

    public function update(string $id, array $updateSubscriptionValues): SubscriptionValue
    {
        $subscription = Subscription::findOrFail($id);
        $subscription->update($updateSubscriptionValues);

        return SubscriptionValue::from($subscription);
    }

    public function delete(string $id): SubscriptionValue
    {
        $subscription = Subscription::findOrFail($id);
        $subscription->delete();

        return SubscriptionValue::from($subscription->refresh());
    }

    /**
     * getByStatuses returns subscriptions by the given store id and status in reverse-chronological order
     *  by creation date
     *
     * @param array $statuses Array of SubscriptionStatus enums to get subscriptions by
     * @throws InvalidArgumentException If any of the given statuses is not a SubscriptionStatus enum
     *
     * @psalm-suppress InvalidReturnType
     * @psalm-suppress InvalidReturnStatement
     * @psalm-suppress InvalidArgument
     */
    public function getByStatus(SubscriptionStatus ...$statuses): SubscriptionCollection
    {
        return SubscriptionValue::collection(
            Subscription::whereIn('status', $statuses)
                ->orderBy('created_at', 'desc')
                ->get()
        );
    }
}
