<?php
declare(strict_types=1);

namespace App\Domain\Shopify\Services;

use App\Domain\Shared\Exceptions\Shopify\ShopifyAuthenticationException;
use App\Domain\Shared\Exceptions\Shopify\ShopifyClientException;
use App\Domain\Shared\Exceptions\Shopify\ShopifyServerException;
use App\Domain\Shopify\Enums\JobStatus;
use App\Domain\Shopify\Enums\JobType;
use App\Domain\Shopify\Exceptions\InternalShopifyRequestException;
use App\Domain\Shopify\Repositories\JobRepository;
use App\Domain\Shopify\Values\Job as JobValue;
use App\Domain\Shopify\Values\WebhookBulkOperationsFinish as WebhookBulkOperationsFinishValue;
use App\Exceptions\NotImplementedException;

class JobsService
{
    public function __construct(
        protected JobRepository $jobRepository,
        protected ShopifyJobsService $shopifyJobsService
    ) {
    }

    public function create(JobValue $jobValue): JobValue
    {
        if ($jobValue->type === JobType::MUTATION) {
            throw new NotImplementedException(
                __('Mutation jobs are not supported yet.'),
            );
        }

        try {
            $jobWithShopifyIds = $this->shopifyJobsService->createQuery($jobValue);
        } catch (ShopifyClientException|ShopifyServerException|ShopifyAuthenticationException $e) {
            throw new InternalShopifyRequestException(
                __('Internal call to Shopify failed, please try again in a few minutes.'),
                $e,
            );
        }

        return $this->jobRepository->store($jobWithShopifyIds);
    }

    public function updateOnBulkOperationsFinish(WebhookBulkOperationsFinishValue $webhookBulkOperationsFinishValue): JobValue
    {
        if ($webhookBulkOperationsFinishValue->type === JobType::MUTATION) {
            throw new NotImplementedException(
                __('Mutation jobs are not supported yet.'),
            );
        }

        $job = $this->jobRepository->getByShopifyJobId($webhookBulkOperationsFinishValue->adminGraphqlApiId);
        $job->status = $webhookBulkOperationsFinishValue->status;
        $job->errorCode = $webhookBulkOperationsFinishValue->errorCode;

        if ($job->status !== JobStatus::COMPLETED) {
            return $this->jobRepository->update($job->id, $job);
        }

        $jobWithFile = $this->shopifyJobsService->getJobData($job);

        return $this->jobRepository->update($jobWithFile->id, $jobWithFile);
    }
}
