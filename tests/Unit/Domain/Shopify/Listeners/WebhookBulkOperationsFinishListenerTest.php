<?php
declare(strict_types=1);

namespace Tests\Unit\Domain\Shopify\Listeners;

use App;
use App\Domain\Shopify\Enums\JobErrorCode;
use App\Domain\Shopify\Enums\JobStatus;
use App\Domain\Shopify\Enums\JobType;
use App\Domain\Shopify\Events\JobUpdatedEvent;
use App\Domain\Shopify\Listeners\WebhookBulkOperationsFinishListener;
use App\Domain\Shopify\Models\Job;
use App\Domain\Shopify\Values\JwtPayload;
use App\Domain\Shopify\Values\JwtToken;
use App\Domain\Shopify\Values\WebhookBulkOperationsFinish as WebhookBulkOperationsFinishValue;
use App\Domain\Stores\Models\Store;
use Config;
use Event;
use Firebase\JWT\JWT;
use Illuminate\Http\Client\Request;
use Illuminate\Support\Facades\Http;
use Tests\Fixtures\Domains\Shopify\Traits\ShopifyBulkOperationResponsesTestData;
use Tests\TestCase;

class WebhookBulkOperationsFinishListenerTest extends TestCase
{
    use ShopifyBulkOperationResponsesTestData;

    protected Store $store;

    protected function setUp(): void
    {
        parent::setUp();

        Config::set('services.shopify.client_secret', 'super-secret-key');

        $this->store = Store::withoutEvents(function () {
            return Store::factory()->create();
        });

        App::context(store: $this->store);
        App::context(jwtToken: new JwtToken(JWT::encode(
            (new JwtPayload(domain: $this->store->domain))->toArray(),
            config('services.shopify.client_secret'),
            'HS256'
        )));
    }

    public function testItDoesNotHandleEventOnMutation(): void
    {
        Event::fake([
            JobUpdatedEvent::class,
        ]);
        Http::fake();

        Job::withoutEvents(function () {
            return Job::factory()->shopifyBulkOperationCreated()->create([
                'store_id' => App::context()->store->id,
                'type' => JobType::MUTATION,
            ]);
        });

        $webhookBulkOperationsFinishValue = WebhookBulkOperationsFinishValue::builder()->mutation()->create();

        $webhookBulkOperationsFinishListener = resolve(WebhookBulkOperationsFinishListener::class);
        $webhookBulkOperationsFinishListener->handle($webhookBulkOperationsFinishValue);

        Http::assertSentCount(0);
        Event::assertNotDispatched(JobUpdatedEvent::class);
    }

    public function testItHandlesEventOnJobFailed(): void
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

        $webhookBulkOperationsFinishListener = resolve(WebhookBulkOperationsFinishListener::class);
        $webhookBulkOperationsFinishListener->handle($webhookBulkOperationsFinishValue);

        Event::assertDispatched(JobUpdatedEvent::class, function (JobUpdatedEvent $event) {
            $this->assertEquals('', $event->job->exportFileUrl);
            $this->assertEquals('', $event->job->exportPartialFileUrl);
            $this->assertEquals(JobStatus::FAILED, $event->job->status);
            $this->assertEquals(JobErrorCode::ACCESS_DENIED, $event->job->errorCode);

            return true;
        });

        Http::assertSentCount(0);
    }

    public function testItHandlesEventOnJobComplete(): void
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

        $webhookBulkOperationsFinishListener = resolve(WebhookBulkOperationsFinishListener::class);
        $webhookBulkOperationsFinishListener->handle($webhookBulkOperationsFinishValue);

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
