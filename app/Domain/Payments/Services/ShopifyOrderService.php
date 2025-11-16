<?php
declare(strict_types=1);

namespace App\Domain\Payments\Services;

use App\Domain\Shared\Services\ShopifyGraphqlService;

class ShopifyOrderService
{
    public function __construct(protected ShopifyGraphqlService $shopifyGraphqlService)
    {
    }

    public function isOrderArchived(string $sourceOrderId): bool
    {
        $query = /** @lang GraphQL */
            <<<'QUERY'
            query order($orderId: ID!) {
                order(id: $orderId) {
                    id
                    closed
                }
            }
        QUERY;

        $response = $this->shopifyGraphqlService->post($query, ['orderId' => $sourceOrderId]);

        return $response['data']['order']['closed'];
    }

    public function openOrder(string $sourceOrderId): bool
    {
        $mutation = /** @lang GraphQL */
            <<<'QUERY'
            mutation orderOpen($orderId: ID!) {
                orderOpen(input: {id: $orderId}) {
                    userErrors {
                        field
                        message
                    }
                    order {
                        id
                    }
                }
            }
        QUERY;

        $this->shopifyGraphqlService->postMutation($mutation, ['orderId' => $sourceOrderId]);

        return true;
    }
}
