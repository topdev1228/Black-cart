<?php
declare(strict_types=1);

namespace App\Domain\Programs\Services;

use App\Domain\Shared\Services\ShopifyGraphqlService;

class ShopifyProgramVariantService
{
    public function __construct(protected ShopifyGraphqlService $shopifyGraphqlService)
    {
    }

    public function addProductVariants(string $sellingPlanGroupId, array $ids): void
    {
        $this->syncProductsOrVariantsToSellingPlanGroup(
            $sellingPlanGroupId,
            'sellingPlanGroupAddProductVariants',
            'productVariantIds',
            $ids,
        );
    }

    public function removeProductVariants(string $sellingPlanGroupId, array $ids): void
    {
        $this->syncProductsOrVariantsToSellingPlanGroup(
            $sellingPlanGroupId,
            'sellingPlanGroupRemoveProductVariants',
            'productVariantIds',
            $ids,
        );
    }

    private function syncProductsOrVariantsToSellingPlanGroup(
        string $sellingPlanGroupId,
        string $mutation,
        string $idKey,
        array $ids
    ): void {
        if (empty($ids)) {
            return;
        }

        $idsString = implode('", "', $ids);
        $idsString = '"' . $idsString . '"';

        $queryString = <<<QUERY
            mutation {
              {$mutation}(
                id: "{$sellingPlanGroupId}"
                {$idKey}: [{$idsString}]
              ) {
                userErrors {
                  field
                  message
                }
              }
            }
            QUERY;
        $this->shopifyGraphqlService->postMutation($queryString);
    }

    public function variantsInSellingPlan($id, $variantIds)
    {
        $fieldSelectors = array_map(
            function ($item) {
                $variantId = explode('/', $item);
                $variantId = end($variantId);

                return "productVariant{$variantId}: appliesToProductVariant(productVariantId: \"$item\")";
            },
            $variantIds
        );

        $fieldSelectorsString = implode("\n", $fieldSelectors);

        $queryString = <<<QUERY
        {
            sellingPlanGroup(id: "{$id}") {
                {$fieldSelectorsString}
            }
         }
        QUERY;

        return $this->shopifyGraphqlService->postMutation($queryString);
    }
}
