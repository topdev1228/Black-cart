<?php
declare(strict_types=1);

namespace App\Domain\Shopify\Services;

use App\Domain\Shared\Services\ShopifyGraphqlService;
use App\Domain\Shopify\Enums\JobStatus;
use App\Domain\Shopify\Values\Job as JobValue;
use Str;

class ShopifyJobsService
{
    public function __construct(protected ShopifyGraphqlService $shopifyGraphqlService)
    {
    }

    public function createQuery(JobValue $jobValue): JobValue
    {
        $createJobQuery = <<<QUERY
        mutation {
          bulkOperationRunQuery(
           query: """
            {$jobValue->query}
            """
          ) {
            bulkOperation {
              id
              status
            }
            userErrors {
              field
              message
            }
          }
        }
        QUERY;

        $response = $this->shopifyGraphqlService->postMutation($createJobQuery);

        return new JobValue(
            storeId: $jobValue->storeId,
            type: $jobValue->type,
            query: $jobValue->query,
            domain: $jobValue->domain,
            topic: $jobValue->topic,
            shopifyJobId: $response['data']['bulkOperationRunQuery']['bulkOperation']['id'],
            status: JobStatus::tryFrom(
                Str::lower($response['data']['bulkOperationRunQuery']['bulkOperation']['status']),
            ),
        );
    }

    public function getJobData(JobValue $jobValue): JobValue
    {
        $query = <<<QUERY
        query {
          node(id: "{$jobValue->shopifyJobId}") {
            ... on BulkOperation {
              url
              partialDataUrl
            }
          }
        }
        QUERY;

        $response = $this->shopifyGraphqlService->post($query);

        $jobValue->exportFileUrl = $response['data']['node']['url'];
        $jobValue->exportPartialFileUrl = $response['data']['node']['partialDataUrl'];

        return $jobValue;
    }
}
