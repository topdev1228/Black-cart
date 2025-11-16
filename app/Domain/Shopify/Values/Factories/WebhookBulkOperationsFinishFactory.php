<?php
declare(strict_types=1);

namespace App\Domain\Shopify\Values\Factories;

use App\Domain\Shared\Values\Factory;

class WebhookBulkOperationsFinishFactory extends Factory
{
    public function definition(): array
    {
        return [
            'admin_graphql_api_id' => 'gid://shopify/BulkOperation/1',
            'completed_at' => '2024-01-09T05:54:12-05:00',
            'created_at' => '2024-01-09T05:54:12-05:00',
            'error_code' => null,
            'status' => 'completed',
            'type' => 'query',
        ];
    }

    public function mutation(): static
    {
        return $this->state([
            'type' => 'mutation',
        ]);
    }

    public function errorAccessDenied(): static
    {
        return $this->state([
            'status' => 'failed',
            'error_code' => 'ACCESS_DENIED',
        ]);
    }
}
