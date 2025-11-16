<?php
declare(strict_types=1);

namespace Tests\Feature\Domain\Trials\Http\Controllers;

use App;
use App\Domain\Shopify\Values\JwtPayload;
use App\Domain\Stores\Models\Store;
use App\Domain\Trials\Models\Trialable as TrialableModel;
use App\Domain\Trials\Repositories\TrialableRepository;
use App\Domain\Trials\Values\Trialable as TrialableValue;
use Config;
use Firebase\JWT\JWT;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Database\Eloquent\ModelNotFoundException;
use Tests\TestCase;

class TrialsControllerTest extends TestCase
{
    const TEST_SOURCE_KEY = '012345';

    protected TrialableRepository $repository;
    private Store $currentStore;
    private array $headers;

    protected function setUp(): void
    {
        parent::setUp();
        $this->repository = $this->partialMock(TrialableRepository::class);

        $this->currentStore = Store::withoutEvents(function () {
            return Store::factory()->create();
        });
        App::context(store: $this->currentStore);

        Config::set('services.shopify.client_secret', 'super-secret-key');

        $this->headers = [
            'Authorization' => 'Bearer ' . JWT::encode(
                (new JwtPayload(domain: $this->currentStore->domain))->toArray(),
                config('services.shopify.client_secret'),
                'HS256'
            ),
        ];
    }

    /**
     * A basic unit test example.
     */
    public function testTrialsIndex(): void
    {
        $this->repository->shouldReceive('all')->once()->andReturn(new Collection());
        $response = $this->getJson(route('trials.api.trials.index'), $this->headers);

        $response->assertStatus(200);
        $response->assertJsonStructure([
            'trials' => [],
        ]);
        $response->assertJsonCount(0, 'trials');
    }

    public function testStoreTrial(): void
    {
        $trialData = [
            'source_key' => self::TEST_SOURCE_KEY,
            'source_id' => 'order01234',
        ];

        $this->repository->shouldReceive('save')->andReturn(TrialableValue::from($trialData));

        $response = $this->postJson(route('trials.api.trials.store'), $trialData, $this->headers);
        $response->assertStatus(201);
    }

    public function testStoreTrialMissingParams(): void
    {
        $trialData = [
            'source_key' => self::TEST_SOURCE_KEY,
        ];

        $response = $this->postJson(route('trials.api.trials.store'), $trialData, $this->headers);
        $response->assertStatus(422);
    }

    public function testShowTrial(): void
    {
        $trial = TrialableModel::factory()->create();

        $this->repository->shouldReceive('getById')->andReturn(TrialableValue::from($trial));

        $response = $this->getJson(route('trials.api.trials.show', [
            'trial' => $trial,
        ]), $this->headers);

        $response->assertStatus(200);
        $response->assertJsonStructure([
            'trial' => [],
        ]);
    }

    public function testShowTrialNotFound(): void
    {
        $this->repository->shouldReceive('getById')->andThrow(new ModelNotFoundException('No query results for model [App\Domain\Trials\Models\Trialable]'));

        $response = $this->getJson(route('trials.api.trials.show', [
            'trial' => 'not-a-valid-id-string',
        ]), $this->headers);

        $response->assertStatus(404);
        $response->assertJsonFragment([
            'type' => 'request_error',
            'code' => 'resource_not_found',
            'message' => 'Trialable not found.',
        ]);
    }

    public function testCreateNewTrial(): void
    {
        $trialData = [
            'source_key' => self::TEST_SOURCE_KEY,
            'source_id' => 'order01234',
        ];

        $this->repository->shouldReceive('save')->andReturn(TrialableValue::from($trialData));
        $response = $this->postJson(route('trials.api.trials.store'), $trialData, $this->headers);

        $response->assertStatus(201);
    }

    public function testUpdateExistingTrial(): void
    {
        $trialData = [
            'source_key' => self::TEST_SOURCE_KEY,
            'source_id' => 'order01234',
        ];

        $model = TrialableModel::factory()->create($trialData);

        $trialData['title'] = 'Order#01234';

        $this->repository->shouldReceive('save')->andReturn(TrialableValue::from($trialData));
        $response = $this->putJson(route('trials.api.trials.update', [
            'trial' => $model->id,
        ]), $trialData, $this->headers);

        $response->assertStatus(200);
    }

    public function testUpdateExistingTrialNoop(): void
    {
        $trialData = [
            'id' => \Str::uuid(),
            'source_key' => self::TEST_SOURCE_KEY,
            'source_id' => 'order01234',
            'title' => 'Order#01234',
        ];

        $model = TrialableModel::factory()->create($trialData);

        $response = $this->putJson(route('trials.api.trials.update', [
            'trial' => $model->refresh()->id,
        ]), $trialData, $this->headers);

        $response->assertStatus(200);

        $this->assertEquals(
            TrialableValue::from($model)->toArray(),
            $response->json()['trial']
        );
    }
}
