<?php
declare(strict_types=1);

namespace Tests\Feature\Domain\Shared\Jobs\Middleware;

use App\Domain\Stores\Repositories\StoreRepository;
use App\Domain\Stores\Values\Store;
use Illuminate\Support\Facades\App;
use Mockery\MockInterface;
use Tests\Fixtures\Jobs\CurrentStoreJob;
use Tests\TestCase;

class CurrentStoreTest extends TestCase
{
    public function testItSetsScope(): void
    {
        $store = Store::builder()->create(['id' => 'test-store-id']);

        $this->mock(StoreRepository::class, function (MockInterface $mock) use ($store) {
            $mock->shouldReceive('getByIdUnsafe')->with($store->id)->andReturn($store);
        });

        App::context(store: $store); // Is unset in the constructor of CurrentStoreJob after the metadata is set
        CurrentStoreJob::dispatchSync();
    }
}
