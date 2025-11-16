<?php
declare(strict_types=1);

namespace App\Domain\Shopify\Values\Factories;

use App\Domain\Shared\Values\Factory;
use App\Domain\Shopify\Enums\JobErrorCode;
use App\Domain\Shopify\Enums\JobStatus;
use App\Domain\Shopify\Enums\JobType;

class JobFactory extends Factory
{
    public function definition(): array
    {
        return [
            'store_id' => 'store_id_1',
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

    public function completed(): static
    {
        return $this->state([
            'shopify_job_id' => 'gid://shopify/BulkOperation/1',
            'export_file_url' => 'https://shopify.com/data.jsonl',
            'export_partial_file_url' => null,
            'status' => JobStatus::COMPLETED,
            'error_code' => null,
        ]);
    }

    public function failed(): static
    {
        return $this->state([
            'shopify_job_id' => 'gid://shopify/BulkOperation/1',
            'export_file_url' => null,
            'export_partial_file_url' => null,
            'status' => JobStatus::FAILED,
            'error_code' => JobErrorCode::INTERNAL_SERVER_ERROR,
        ]);
    }

    public function mutation(): static
    {
        return $this->state([
            'type' => JobType::MUTATION,
        ]);
    }
}
