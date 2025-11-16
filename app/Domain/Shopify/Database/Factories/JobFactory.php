<?php
declare(strict_types=1);

namespace App\Domain\Shopify\Database\Factories;

use App\Domain\Shopify\Enums\JobStatus;
use App\Domain\Shopify\Enums\JobType;
use Illuminate\Database\Eloquent\Factories\Factory;

class JobFactory extends Factory
{
    public function definition(): array
    {
        return [
            'store_id' => $this->faker->uuid(),
            'query' => '{
              products {
                edges {
                  node {
                    id
                    createdAt
                    updatedAt
                    title
                    handle
                    description
                    productType
                    options {
                      name
                      position
                      values
                    }
                    priceRange {
                      minVariantPrice {
                        amount
                        currencyCode
                      }
                      maxVariantPrice {
                        amount
                        currencyCode
                      }
                    }
                  }
                }
              }
            }',
            'type' => JobType::QUERY,
            'domain' => 'products',
            'topic' => 'products/import',
            'shopify_job_id' => null,
            'export_file_url' => null,
            'export_partial_file_url' => null,
            'status' => null,
            'error_code' => null,
        ];
    }

    public function shopifyBulkOperationCreated(): static
    {
        return $this->state([
            'shopify_job_id' => 'gid://shopify/BulkOperation/1',
            'export_file_url' => null,
            'export_partial_file_url' => null,
            'status' => JobStatus::CREATED,
            'error_code' => null,
        ]);
    }
}
