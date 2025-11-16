<?php
declare(strict_types=1);

namespace Tests\Unit\Domain\Shopify\Services;

use App;
use App\Domain\Shopify\Enums\JobErrorCode;
use App\Domain\Shopify\Enums\JobStatus;
use App\Domain\Shopify\Events\JobUpdatedEvent;
use App\Domain\Shopify\Models\Job;
use App\Domain\Shopify\Services\JobsService;
use App\Domain\Shopify\Values\Job as JobValue;
use App\Domain\Shopify\Values\WebhookBulkOperationsFinish as WebhookBulkOperationsFinishValue;
use App\Domain\Stores\Models\Store;
use App\Exceptions\NotImplementedException;
use Event;
use Illuminate\Database\Eloquent\ModelNotFoundException;
use Illuminate\Http\Client\Request;
use Illuminate\Support\Facades\Http;
use Tests\Fixtures\Domains\Shopify\Traits\ShopifyBulkOperationResponsesTestData;
use Tests\TestCase;

class JobsServiceTest extends TestCase
{
    use ShopifyBulkOperationResponsesTestData;

    protected Store $currentStore;
    protected JobsService $jobsService;

    protected function setUp(): void
    {
        parent::setUp();

        $this->currentStore = Store::withoutEvents(function () {
            return Store::factory()->create();
        });
        App::context(store: $this->currentStore);

        $this->jobsService = resolve(JobsService::class);
    }

    public function testItCreatesJob(): void
    {
        $jobValue = JobValue::builder()->create([
            'store_id' => $this->currentStore->id,
        ]);

        Http::fake([
            App::context()->store->domain . '/admin/api/*/graphql.json' => Http::sequence()
                ->push(static::getShopifyBulkOperationCreateSuccessResponse()),
        ]);

        $jobActual = $this->jobsService->create($jobValue);

        Http::assertSent(function (Request $request) {
            // TODO: validate graphql query
            return $request->method() === 'POST' &&
                $request->hasHeader('X-Shopify-Access-Token', App::context()->store->accessToken);
        });

        $expectedJobValue = JobValue::builder()->shopifyBulkOperationCreated()->create([
            'id' => $jobActual->id,
            'store_id' => $this->currentStore->id,
        ]);

        $this->assertNotEmpty($jobActual->id);
        $this->assertEquals($expectedJobValue->query, $jobActual->query);
        $this->assertEquals($expectedJobValue->domain, $jobActual->domain);
        $this->assertEquals($expectedJobValue->topic, $jobActual->topic);
        $this->assertEquals($expectedJobValue->shopifyJobId, $jobActual->shopifyJobId);
        $this->assertEquals($expectedJobValue->status, $jobActual->status);

        $this->assertDatabaseHas('shopify_jobs', $expectedJobValue->toArray());
    }

    public function testItDoesNotUpdateOnBulkOperationFinishForMutation(): void
    {
        $webhookBulkOperationsFinishValue = WebhookBulkOperationsFinishValue::builder()->mutation()->create();

        $this->expectException(NotImplementedException::class);
        $this->expectExceptionMessage('Mutation jobs are not supported yet.');

        $this->jobsService->updateOnBulkOperationsFinish($webhookBulkOperationsFinishValue);
    }

    public function testItDoesNotUpdateOnBulkOperationFinishNonExistentJob(): void
    {
        $webhookBulkOperationsFinishValue = WebhookBulkOperationsFinishValue::builder()->create();

        $this->expectException(ModelNotFoundException::class);

        $this->jobsService->updateOnBulkOperationsFinish($webhookBulkOperationsFinishValue);
    }

    public function testItUpdatesOnBulkOperationFinishForFailedStatus(): void
    {
        Event::fake([
            JobUpdatedEvent::class,
        ]);

        Http::fake();

        Job::withoutEvents(function () {
            return Job::factory()->shopifyBulkOperationCreated()->create([
                'store_id' => App::context()->store->id,
            ]);
        });

        $webhookBulkOperationsFinishValue = WebhookBulkOperationsFinishValue::builder()->errorAccessDenied()->create();
        $this->jobsService->updateOnBulkOperationsFinish($webhookBulkOperationsFinishValue);

        Event::assertDispatched(JobUpdatedEvent::class, function (JobUpdatedEvent $event) {
            $this->assertEquals('', $event->job->exportFileUrl);
            $this->assertEquals('', $event->job->exportPartialFileUrl);
            $this->assertEquals(JobStatus::FAILED, $event->job->status);
            $this->assertEquals(JobErrorCode::ACCESS_DENIED, $event->job->errorCode);

            return true;
        });

        Http::assertSentCount(0);
    }

    public function testItUpdatesOnBulkOperationFinish(): void
    {
        Event::fake([
            JobUpdatedEvent::class,
        ]);

        Http::fake([
            App::context()->store->domain . '/admin/api/*' => Http::sequence()
                ->push(static::getShopifyBulkOperationFinishFileUrlSuccessResponse()),
        ]);

        Job::withoutEvents(function () {
            return Job::factory()->shopifyBulkOperationCreated()->create([
                'store_id' => App::context()->store->id,
            ]);
        });
        $webhookBulkOperationsFinishValue = WebhookBulkOperationsFinishValue::builder()->create();
        $this->jobsService->updateOnBulkOperationsFinish($webhookBulkOperationsFinishValue);

        Event::assertDispatched(JobUpdatedEvent::class, function (JobUpdatedEvent $event) {
            $this->assertEquals('https://shopify.com/data.jsonl', $event->job->exportFileUrl);
            $this->assertEquals('', $event->job->exportPartialFileUrl);
            $this->assertEquals(JobStatus::COMPLETED, $event->job->status);
            $this->assertNull($event->job->errorCode);

            return true;
        });

        Http::assertSentCount(1);
        Http::assertSent(function (Request $request) {
            // TODO: validate graphql query
            return $request->method() === 'POST' &&
                $request->hasHeader('X-Shopify-Access-Token', App::context()->store->accessToken);
        });
    }
}
