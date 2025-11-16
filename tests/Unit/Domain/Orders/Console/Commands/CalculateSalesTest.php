<?php
declare(strict_types=1);

namespace Tests\Unit\Domain\Orders\Console\Commands;

use App;
use App\Domain\Orders\Jobs\CalculateSalesJob;
use App\Domain\Orders\Repositories\OrderRepository;
use Carbon\CarbonImmutable;
use Mockery;
use Queue;
use Tests\TestCase;

class CalculateSalesTest extends TestCase
{
    protected $repository;

    protected function setUp(): void
    {
        parent::setUp();

        $this->repository = $this->mock(OrderRepository::class);
    }

    public function testItDispatchesCalculateSalesJobForEachStore(): void
    {
        Queue::fake();

        $this->repository->shouldReceive('getStoreIdsByDate')
            ->with(Mockery::type(CarbonImmutable::class))
            ->andReturn(['1', '2', '3']);

        $this->artisan('orders:calculate-sales');

        Queue::assertPushed(CalculateSalesJob::class);
    }

    public function testItDoesNothingWhenNoStores(): void
    {
        Queue::fake();

        $this->repository->shouldReceive('getStoreIdsByDate')
            ->with(Mockery::type(CarbonImmutable::class))
            ->andReturn([]);

        $this->artisan('orders:calculate-sales');

        Queue::assertNothingPushed();
    }

    public function testStoreContextIsSet(): void
    {
        Queue::fake();

        $this->repository->shouldReceive('getStoreIdsByDate')
            ->with(Mockery::type(CarbonImmutable::class))
            ->andReturn(['5']);

        $this->artisan('orders:calculate-sales');

        $this->assertEquals('5', App::context()->store->id);
        Queue::assertPushed(CalculateSalesJob::class);
    }
}
