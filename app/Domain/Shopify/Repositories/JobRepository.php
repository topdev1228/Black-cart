<?php
declare(strict_types=1);

namespace App\Domain\Shopify\Repositories;

use App\Domain\Shopify\Models\Job;
use App\Domain\Shopify\Values\Job as JobValue;

class JobRepository
{
    public function store(JobValue $jobValue): JobValue
    {
        return JobValue::from(Job::create($jobValue->toArray()));
    }

    public function getByShopifyJobId(string $shopifyJobId): JobValue
    {
        return JobValue::from(Job::where('shopify_job_id', $shopifyJobId)->firstOrFail());
    }

    public function update(string $id, JobValue $jobValue): JobValue
    {
        $job = Job::findOrFail($id);
        $job->update($jobValue->toArray());

        return JobValue::from($job);
    }
}
