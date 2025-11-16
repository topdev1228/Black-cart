<?php
declare(strict_types=1);

namespace App\Domain\Programs\Services;

use App\Domain\Shared\Services\ShopifyGraphqlService;

class ShopifyProgramProductService
{
    public function __construct(protected ShopifyGraphqlService $shopifyGraphqlService)
    {
    }

    public function removeProducts(string $sellingPlanGroupId, array $ids): void
    {
        $this->syncProductsToSellingPlanGroup(
            $sellingPlanGroupId,
            'sellingPlanGroupRemoveProducts',
            'productIds',
            $ids,
        );
    }

    public function getProducts(string $sellingPlanGroupId)
    {
        $query = <<<QUERY
          query {
            sellingPlanGroup(id: "{$sellingPlanGroupId}") {
              id
              products(first: 1) {
                edges {
                  node {
                    id
                    handle
                  }
                }
              }
            }
          }
        QUERY;

        return $this->shopifyGraphqlService->post($query);
    }

    private function syncProductsToSellingPlanGroup(
        string $sellingPlanGroupId,
        string $mutation,
        string $idKey,
        array $ids
    ): void {
        if (empty($ids)) {
            return;
        }

        $idsString = '"' . implode('", "', $ids) . '"';

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
}
