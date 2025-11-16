<?php
declare(strict_types=1);

namespace Tests\Unit\Domain\Shopify\Repositories;

use App;
use App\Domain\Shopify\Enums\JobStatus;
use App\Domain\Shopify\Models\Job;
use App\Domain\Shopify\Repositories\JobRepository;
use App\Domain\Shopify\Values\Job as JobValue;
use App\Domain\Stores\Models\Store;
use Illuminate\Database\Eloquent\ModelNotFoundException;
use Tests\TestCase;

class JobRepositoryTest extends TestCase
{
    protected Store $currentStore;
    protected JobRepository $jobRepository;

    protected function setUp(): void
    {
        parent::setUp();

        $this->currentStore = Store::withoutEvents(function () {
            return Store::factory()->create();
        });
        App::context(store: $this->currentStore);

        $this->jobRepository = resolve(JobRepository::class);
    }

    public function testItCreatesJob(): void
    {
        $jobValue = JobValue::builder()->shopifyBulkOperationCreated()->create([
            'store_id' => $this->currentStore->id,
        ]);

        $jobActual = $this->jobRepository->store($jobValue);

        $jobValue->id = $jobActual->id;

        $this->assertEquals($jobValue->id, $jobActual->id);
        $this->assertEquals($jobValue->storeId, $jobActual->storeId);
        $this->assertEquals($jobValue->query, $jobActual->query);
        $this->assertEquals($jobValue->type, $jobActual->type);
        $this->assertEquals($jobValue->domain, $jobActual->domain);
        $this->assertEquals($jobValue->topic, $jobActual->topic);
        $this->assertEquals($jobValue->shopifyJobId, $jobActual->shopifyJobId);
        $this->assertEquals($jobValue->exportFileUrl, $jobActual->exportFileUrl);
        $this->assertEquals($jobValue->exportPartialFileUrl, $jobActual->exportPartialFileUrl);
        $this->assertEquals($jobValue->status, $jobActual->status);
        $this->assertEquals($jobValue->errorCode, $jobActual->errorCode);

        $this->assertDatabaseHas('shopify_jobs', $jobValue->toArray());
    }

    public function testItDoesNotUpdateJobNotFound(): void
    {
        $this->expectException(ModelNotFoundException::class);

        $jobValue = JobValue::builder()->shopifyBulkOperationCreated()->create([
            'store_id' => $this->currentStore->id,
        ]);

        $this->jobRepository->update('non-existent-job-id', $jobValue);
    }

    public function testItUpdatesJob(): void
    {
        $job = Job::withoutEvents(function () {
            return Job::factory()->shopifyBulkOperationCreated()->create([
                'store_id' => $this->currentStore->id,
            ]);
        });

        $jobValue = JobValue::from($job->toArray());
        $jobValue->status = JobStatus::COMPLETED;
        $jobValue->exportFileUrl = 'https://shopify.com/data.jsonl';
        $jobValue->exportPartialFileUrl = 'https://shopify.com/partial_data.jsonl';

        $jobActual = $this->jobRepository->update($job->id, $jobValue);

        $this->assertEquals($jobValue->id, $jobActual->id);
        $this->assertEquals($jobValue->storeId, $jobActual->storeId);
        $this->assertEquals($jobValue->query, $jobActual->query);
        $this->assertEquals($jobValue->type, $jobActual->type);
        $this->assertEquals($jobValue->domain, $jobActual->domain);
        $this->assertEquals($jobValue->topic, $jobActual->topic);
        $this->assertEquals($jobValue->shopifyJobId, $jobActual->shopifyJobId);
        $this->assertEquals($jobValue->exportFileUrl, $jobActual->exportFileUrl);
        $this->assertEquals($jobValue->exportPartialFileUrl, $jobActual->exportPartialFileUrl);
        $this->assertEquals($jobValue->status, $jobActual->status);
        $this->assertEquals($jobValue->errorCode, $jobActual->errorCode);

        $this->assertDatabaseHas('shopify_jobs', $jobValue->toArray());
    }
}
