<?php
declare(strict_types=1);

namespace App\Domain\Shopify\Services;

use App\Domain\Shared\Services\ShopifyGraphqlService;
use App\Domain\Shopify\Values\Collections\MetafieldCollection;
use App\Domain\Shopify\Values\Metafield as MetafieldValue;

class ShopifyMetafieldsService
{
    public function __construct(protected ShopifyGraphqlService $shopifyGraphqlService)
    {
    }

    public function upsert(string $shopifyAppInstallationId, MetafieldCollection $metafields): MetafieldCollection
    {
        $metafieldsArray = $metafields->toArray();

        $metafieldsArray = array_map(function ($metafield) use ($shopifyAppInstallationId): array {
            $metafield['namespace'] = 'blackcart';
            $metafield['ownerId'] = $shopifyAppInstallationId;

            // id to be populated from the Shopify response.  Sending this to the graphql input will cause errors.
            unset($metafield['id']);

            return $metafield;
        }, $metafieldsArray);

        $query = 'mutation metafieldsSet($metafields: [MetafieldsSetInput!]!) {
          metafieldsSet(metafields: $metafields) {
            metafields {
                id,
                namespace,
                key,
                value,
                type,
            }
            userErrors {
              field
              message
            }
          }
        }';

        $response = $this->shopifyGraphqlService->postMutation(
            $query,
            ['metafields' => $metafieldsArray]
        );

        return MetafieldValue::collection($response['data']['metafieldsSet']['metafields']);
    }

    public function getAppInstallationId(): string
    {
        $query = 'query {
            currentAppInstallation {
                id
            }
        }';
        $response = $this->shopifyGraphqlService->post($query);

        return $response['data']['currentAppInstallation']['id'];
    }
}
