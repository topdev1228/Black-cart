<?php
declare(strict_types=1);

namespace Tests\Unit\Domain\Orders\Repositories;

use App;
use App\Domain\Orders\Models\TbybNetSale;
use App\Domain\Orders\Repositories\TbybNetSaleRepository;
use App\Domain\Orders\Values\TbybNetSale as TbybNetSaleValue;
use App\Domain\Stores\Models\Store;
use Illuminate\Support\Facades\Date;
use Tests\TestCase;

class TbybNetSaleRepositoryTest extends TestCase
{
    protected Store $store;
    protected TbybNetSaleRepository $repository;

    protected function setUp(): void
    {
        parent::setUp();

        $this->store = Store::factory()->create();
        App::context(store: $this->store);
        $this->repository = resolve(TbybNetSaleRepository::class);
    }

    public function testItCreatesTbybNetSale(): void
    {
        $val = TbybNetSaleValue::builder()->create();
        $this->repository->create($val);

        $this->assertDatabaseCount('orders_tbyb_net_sales', 1);
    }

    public function testItGetsLatest(): void
    {
        $now = Date::now();
        TbybNetSale::factory()->create([
            'created_at' => Date::now()->subMonth(),
            'time_range_start' => Date::now()->subMonth(),
            'store_id' => $this->store->id,
        ]);
        TbybNetSale::factory()->create([
            'created_at' => $now,
            'time_range_start' => $now,
            'store_id' => $this->store->id,
        ]);

        $expected = $this->repository->getLatest();

        $this->assertEquals($expected->timeRangeStart, $now->toDateTimeString());
    }
}
