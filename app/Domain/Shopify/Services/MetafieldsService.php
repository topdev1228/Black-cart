<?php
declare(strict_types=1);

namespace App\Domain\Shopify\Services;

use App\Domain\Shared\Exceptions\Shopify\ShopifyAuthenticationException;
use App\Domain\Shared\Exceptions\Shopify\ShopifyClientException;
use App\Domain\Shared\Exceptions\Shopify\ShopifyServerException;
use App\Domain\Shopify\Enums\StoreStatus;
use App\Domain\Shopify\Exceptions\InternalShopifyRequestException;
use App\Domain\Shopify\Values\Collections\MetafieldCollection;
use App\Domain\Shopify\Values\Metafield as MetafieldValue;
use App\Domain\Shopify\Values\Program as ProgramValue;
use App\Domain\Shopify\Values\Subscription as SubscriptionValue;

class MetafieldsService
{
    public function __construct(protected ShopifyMetafieldsService $shopifyMetafieldsService)
    {
    }

    public function upsertStoreStatusMetefields(StoreStatus $storeStatus): MetafieldCollection
    {
        $metafields = [
            MetafieldValue::builder()->string('store_status', $storeStatus->value)->create(),
        ];

        return $this->upsertMetafields(MetafieldValue::collection($metafields));
    }

    public function upsertSubscriptionStatusMetafield(SubscriptionValue $subscriptionValue): MetafieldCollection
    {
        $metafields = [
            MetafieldValue::builder()->string('subscription_status', $subscriptionValue->status->value)->create(),
        ];

        return $this->upsertMetafields(MetafieldValue::collection($metafields));
    }

    public function upsertProgramMetafields(ProgramValue $programValue): MetafieldCollection
    {
        $metafields = [
            MetafieldValue::builder()->string('program_name', $programValue->name)->create(),
            MetafieldValue::builder()->string(
                'selling_plan_group_id',
                $programValue->shopifySellingPlanGroupId
            )->create(),
            MetafieldValue::builder()->string('selling_plan_id', $programValue->shopifySellingPlanId)
                ->create(),
            MetafieldValue::builder()->integer('try_period_days', $programValue->tryPeriodDays)->create(),
            MetafieldValue::builder()->string('min_tbyb_items', (string) ($programValue->minTbybItems))->create(),
            MetafieldValue::builder()->string(
                'max_tbyb_items',
                $programValue->maxTbybItems === null ? 'unlimited' : (string) ($programValue->maxTbybItems)
            )->create(),
            MetafieldValue::builder()->string('deposit_type', $programValue->depositType->value)->create(),
        ];

        if ($programValue->depositType->value === 'fixed') {
            $metafields[] = MetafieldValue::builder()->money(
                'deposit_fixed',
                $programValue->depositValue / 100,
                $programValue->currency->value,
            )->create();
            // Set the deposit_percentage to 0 if the deposit type is fixed
            $metafields[] = MetafieldValue::builder()->integer('deposit_percentage', 0)->create();
        } else {
            $metafields[] = MetafieldValue::builder()->integer('deposit_percentage', $programValue->depositValue)
                ->create();
            // Set the deposit_fixed to 0 if the deposit type is percentage
            $metafields[] = MetafieldValue::builder()->money(
                'deposit_fixed',
                0,
                $programValue->currency->value,
            )->create();
        }

        return $this->upsertMetafields(MetafieldValue::collection($metafields));
    }

    protected function upsertMetafields(MetafieldCollection $metafields): MetafieldCollection
    {
        try {
            $shopifyAppInstallationId = $this->shopifyMetafieldsService->getAppInstallationId();

            return $this->shopifyMetafieldsService->upsert(
                $shopifyAppInstallationId,
                $metafields,
            );
        } catch (ShopifyClientException|ShopifyServerException|ShopifyAuthenticationException $e) {
            if ($e->getPrevious() !== null && $e->getPrevious()->getCode() === 402) {
                // Fail the call silently because the merchant has uninstalled the app, API key is no longer valid
                return MetafieldValue::collection([]);
            }

            throw new InternalShopifyRequestException(
                __('Internal call to Shopify failed, please try again in a few minutes.'),
                $e,
            );
        }
    }
}
