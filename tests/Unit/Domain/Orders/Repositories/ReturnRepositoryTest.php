<?php
declare(strict_types=1);

namespace Tests\Unit\Domain\Orders\Repositories;

use App;
use App\Domain\Orders\Models\OrderReturn;
use App\Domain\Orders\Repositories\ReturnRepository;
use App\Domain\Orders\Values\OrderReturn as OrderReturnValue;
use App\Domain\Stores\Models\Store;
use Str;
use Tests\TestCase;

class ReturnRepositoryTest extends TestCase
{
    protected Store $store;
    protected ReturnRepository $repository;

    protected function setUp(): void
    {
        parent::setUp();

        $this->store = Store::factory()->create();
        App::context(store: $this->store);
        $this->repository = resolve(ReturnRepository::class);
    }

    public function testItSavesTransaction(): void
    {
        $val = OrderReturnValue::builder()->create();

        $this->repository->save($val);
        $this->assertDatabaseCount('orders_returns', 1);
    }

    public function testGetBySouceId(): void
    {
        $orderReturn = OrderReturn::factory()->create([
            'source_id' => (string) Str::uuid(),
        ]);
        $orderReturnValue = OrderReturnValue::from($orderReturn);
        $return = $this->repository->getBySourceId($orderReturn->source_id);

        $this->assertEquals($orderReturnValue, $return);
    }
}
