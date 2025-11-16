<?php
declare(strict_types=1);

namespace Tests\Unit\Domain\Orders\Repositories;

use App;
use App\Domain\Orders\Repositories\ReturnLineItemRepository;
use App\Domain\Orders\Values\ReturnLineItem as ReturnLineItemValue;
use App\Domain\Stores\Models\Store;
use Tests\TestCase;

class ReturnLineItemRepositoryTest extends TestCase
{
    protected Store $store;
    protected ReturnLineItemRepository $repository;

    protected function setUp(): void
    {
        parent::setUp();

        $this->store = Store::factory()->create();
        App::context(store: $this->store);
        $this->repository = resolve(ReturnLineItemRepository::class);
    }

    public function testItSavesReturnLineItem(): void
    {
        $val = ReturnLineItemValue::builder()->create();

        $this->repository->save($val);
        $this->assertDatabaseCount('orders_returns_line_items', 1);
    }
}
