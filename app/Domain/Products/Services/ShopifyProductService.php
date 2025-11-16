<?php
declare(strict_types=1);

namespace App\Domain\Products\Services;

use App\Domain\Shared\Services\ShopifyGraphqlService;

class ShopifyProductService
{
    public function __construct(protected ShopifyGraphqlService $shopifyGraphqlService)
    {
    }

    public function getProduct(string $id)
    {
        $query = <<<QUERY
          query {
            product(id: "gid://shopify/Product/$id") {
              title
              description
              onlineStoreUrl
              variants(first: 50) {
                edges {
                  node {
                    id
                    displayName
                  }
                }
              }
            }
          }
        QUERY;

        return $this->shopifyGraphqlService->post($query);
    }
}
