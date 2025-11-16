<?php
declare(strict_types=1);

namespace App\Domain\Billings\Services;

use App\Domain\Billings\Enums\SubscriptionStatus;
use App\Domain\Billings\Values\ShopifyAppSubscription as ShopifyAppSubscriptionValue;
use App\Domain\Billings\Values\ShopifyCurrentAppInstallation as ShopifyCurrentAppInstallationValue;
use App\Domain\Shared\Services\ShopifyGraphqlService;
use Str;

class ShopifyCurrentAppInstallationService
{
    public function __construct(protected ShopifyGraphqlService $shopifyGraphqlService)
    {
    }

    public function get(): ShopifyCurrentAppInstallationValue
    {
        $query = 'query {
            currentAppInstallation {
                id,
                activeSubscriptions {
                    id
                    status
                }
            }
        }';
        $response = $this->shopifyGraphqlService->post($query);

        $appSubscriptions = [];
        foreach ($response['data']['currentAppInstallation']['activeSubscriptions'] as $activeSubscription) {
            $appSubscriptions[] = new ShopifyAppSubscriptionValue(
                id: $activeSubscription['id'],
                status: SubscriptionStatus::tryFrom(Str::lower($activeSubscription['status'])),
            );
        }

        return new ShopifyCurrentAppInstallationValue(
            id: $response['data']['currentAppInstallation']['id'],
            activeSubscriptions: ShopifyAppSubscriptionValue::collection($appSubscriptions),
        );
    }
}
