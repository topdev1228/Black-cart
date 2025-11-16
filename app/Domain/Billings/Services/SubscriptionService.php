<?php
declare(strict_types=1);

namespace App\Domain\Billings\Services;

use App\Domain\Billings\Enums\SubscriptionStatus;
use App\Domain\Billings\Events\SubscriptionActivatedEvent;
use App\Domain\Billings\Events\SubscriptionDeactivatedEvent;
use App\Domain\Billings\Exceptions\ActiveSubscriptionNotFoundException;
use App\Domain\Billings\Repositories\SubscriptionLineItemRepository;
use App\Domain\Billings\Repositories\SubscriptionRepository;
use App\Domain\Billings\Values\Collections\SubscriptionCollection;
use App\Domain\Billings\Values\Subscription;
use App\Domain\Billings\Values\Subscription as SubscriptionValue;
use App\Domain\Billings\Values\SubscriptionLineItem;
use App\Domain\Billings\Values\WebhookShopifyAppSubscriptionsUpdate as ShopifyAppSubscriptionsUpdateWebhookValue;
use App\Domain\Shared\Exceptions\Shopify\ShopifyAuthenticationException;
use App\Domain\Shared\Exceptions\Shopify\ShopifyClientException;
use App\Domain\Shared\Exceptions\Shopify\ShopifyServerException;
use App\Domain\Shopify\Exceptions\InternalShopifyRequestException;
use Carbon\CarbonImmutable;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\Date;

class SubscriptionService
{
    public function __construct(
        protected SubscriptionRepository $subscriptionRepository,
        protected SubscriptionLineItemRepository $subscriptionLineItemRepository,
        protected ShopifySubscriptionService $shopifySubscriptionService
    ) {
    }

    public function create(SubscriptionValue $subscriptionValue, string $storeDomain): SubscriptionValue
    {
        try {
            $subscriptionWithShopifyIds = $this->shopifySubscriptionService->create($subscriptionValue, $storeDomain);
        } catch (ShopifyClientException|ShopifyServerException|ShopifyAuthenticationException $e) {
            throw new InternalShopifyRequestException(
                __('Internal call to Shopify failed, please try again in a few minutes.'),
                $e,
            );
        }

        $subscriptionSaved = $this->subscriptionRepository->store($subscriptionWithShopifyIds);

        $subscriptionLineItemsSaved = Collection::empty();
        if ($subscriptionWithShopifyIds->subscriptionLineItems) {
            foreach ($subscriptionWithShopifyIds->subscriptionLineItems as $lineItemValue) {
                $lineItemValue->subscriptionId = $subscriptionSaved->id;
                $subscriptionLineItemsSaved->push(
                    $this->subscriptionLineItemRepository->store($lineItemValue)
                );
            }
        }

        $subscriptionSaved->subscriptionLineItems = SubscriptionLineItem::collection($subscriptionLineItemsSaved);

        return $subscriptionSaved;
    }

    public function getByShopifyAppSubscriptionId(string $shopifyAppSubscriptionId): SubscriptionValue
    {
        return $this->subscriptionRepository->getByShopifyAppSubscriptionId($shopifyAppSubscriptionId);
    }

    public function updateFromShopifyWebhook(ShopifyAppSubscriptionsUpdateWebhookValue $shopifyAppSubscriptionsUpdateWebhookValue): SubscriptionValue
    {
        $subscriptionValue = $this->getByShopifyAppSubscriptionId($shopifyAppSubscriptionsUpdateWebhookValue->appSubscription->id);

        // If status hasn't changed and is active, this means a new subscription recurring charge is processed.
        // This moves the current period end to the next billing date
        if ($subscriptionValue->status === $shopifyAppSubscriptionsUpdateWebhookValue->appSubscription->status &&
            $shopifyAppSubscriptionsUpdateWebhookValue->appSubscription->status === SubscriptionStatus::ACTIVE) {
            $newCurrentPeriodEnd = $this->shopifySubscriptionService->getCurrentPeriodEndByAppSubscriptionId(
                $subscriptionValue->shopifyAppSubscriptionId
            );
            if ($newCurrentPeriodEnd && $subscriptionValue->currentPeriodEnd->notEqualTo($newCurrentPeriodEnd)) {
                return $this->subscriptionRepository->update(
                    $subscriptionValue->id,
                    ['current_period_end' => $newCurrentPeriodEnd],
                );
            }

            return $subscriptionValue;
        }

        return $this->updateStatus(
            $subscriptionValue,
            $shopifyAppSubscriptionsUpdateWebhookValue->appSubscription->status,
            $shopifyAppSubscriptionsUpdateWebhookValue->appSubscription->updatedAt,
        );
    }

    public function getActiveSubscription(): Subscription
    {
        return $this->subscriptionRepository->getByStatus(
            SubscriptionStatus::ACTIVE,
        )->first() ?? throw new ActiveSubscriptionNotFoundException();
    }

    /**
     * cancelActiveSubscriptions Cancels all active subscriptions for the given store
     *
     * @psalm-suppress InvalidReturnType
     * @psalm-suppress InvalidReturnStatement
     * @psalm-suppress InvalidArgument
     */
    public function cancelActiveSubscriptions(): SubscriptionCollection
    {
        $subscriptionValues = $this->subscriptionRepository->getByStatus(
            SubscriptionStatus::ACTIVE,
        );

        $cancelledSubscriptions = [];
        foreach ($subscriptionValues as $subscriptionValue) {
            $cancelledSubscriptions[] = $this->updateStatus(
                $subscriptionValue,
                SubscriptionStatus::CANCELLED,
                Date::now(),
            );
        }

        return SubscriptionValue::collection($cancelledSubscriptions);
    }

    public function updateStatus(
        SubscriptionValue $subscriptionValue,
        SubscriptionStatus $newStatus,
        CarbonImmutable $updatedAt,
    ): SubscriptionValue {
        if ($subscriptionValue->status === $newStatus) {
            return $subscriptionValue;
        }

        try {
            $currentActiveSubscriptionValue = $this->getActiveSubscription();
        } catch (ActiveSubscriptionNotFoundException $e) {
            $currentActiveSubscriptionValue = null;
        }

        $oldStatus = $subscriptionValue->status;
        $updates = [
            'status' => $newStatus,
        ];

        if ($newStatus === SubscriptionStatus::ACTIVE) {
            $updates['activated_at'] = $updatedAt;
            $updates['deactivated_at'] = null;

            // Trials are only for activating a new subscription, meaning from pending to active
            if ($subscriptionValue->status === SubscriptionStatus::PENDING && $subscriptionValue->trialDays > 0) {
                // Trial period end = the date when the subscription became active + trial days
                // See https://community.shopify.com/c/billing-api/graphql-billing-api-issue/m-p/718950 for the formula
                // addDays() changes the original $updatedAt object, so we need to copy it first before adding days
                $updates['trial_period_end'] = $updatedAt->copy()->addDays($subscriptionValue->trialDays);
            }

            // Get the new current period end once the subscription is active
            $newCurrentPeriodEnd = $this->shopifySubscriptionService->getCurrentPeriodEndByAppSubscriptionId(
                $subscriptionValue->shopifyAppSubscriptionId
            );
            if ($newCurrentPeriodEnd) {
                $updates['current_period_end'] = $newCurrentPeriodEnd;
            }
        } elseif (in_array($newStatus, [
            SubscriptionStatus::CANCELLED, SubscriptionStatus::EXPIRED,
            SubscriptionStatus::DECLINED, SubscriptionStatus::FROZEN,
        ])) {
            $updates['deactivated_at'] = $updatedAt;
        }

        $updatedSubscriptionValue = $this->subscriptionRepository->update(
            $subscriptionValue->id,
            $updates,
        );

        // If the new status is a terminal status, soft delete them in the database
        if (in_array($newStatus, [
            SubscriptionStatus::CANCELLED, SubscriptionStatus::EXPIRED, SubscriptionStatus::DECLINED,
        ])) {
            $this->subscriptionRepository->delete($updatedSubscriptionValue->id);
        }

        // We only want to dispatch activation and deactivation events if the subscription status update was on the
        // current active subscription and not on any transitory subscription entries that we don't care about.
        if ($oldStatus !== SubscriptionStatus::ACTIVE && $newStatus === SubscriptionStatus::ACTIVE) {
            SubscriptionActivatedEvent::dispatch($updatedSubscriptionValue);
        } elseif ($oldStatus === SubscriptionStatus::ACTIVE && $newStatus !== SubscriptionStatus::ACTIVE && $currentActiveSubscriptionValue && $currentActiveSubscriptionValue->id === $updatedSubscriptionValue->id) {
            SubscriptionDeactivatedEvent::dispatch($updatedSubscriptionValue);
        }

        return $updatedSubscriptionValue;
    }
}
