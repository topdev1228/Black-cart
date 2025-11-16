<?php
declare(strict_types=1);

namespace Tests\Unit\Domain\Programs\Services;

use App;
use App\Domain\Programs\Models\Program;
use App\Domain\Programs\Services\ProgramProductService;
use App\Domain\Programs\Services\ShopifyProgramProductService;
use App\Domain\Stores\Models\Store;
use Tests\Fixtures\Domains\Programs\Traits\ShopifySellingPlanGroupResponsesTestData;
use Tests\TestCase;

class ProgramProductServiceTest extends TestCase
{
    use ShopifySellingPlanGroupResponsesTestData;

    protected Store $currentStore;

    protected function setUp(): void
    {
        parent::setUp();
        $this->currentStore = Store::factory()->create();
        App::context(store: $this->currentStore);
    }

    public function testItGetsProductInSellingPlan(): void
    {
        $shopifyProgramProductServiceMock = $this->mock(ShopifyProgramProductService::class);
        $shopifyProgramProductServiceMock->shouldReceive('getProducts')->andReturn(static::getSellingPlanProductResponse());
        $programProductService = resolve(ProgramProductService::class);
        $program = Program::factory()->create(['store_id' => $this->currentStore->id, 'shopify_selling_plan_group_id' => '123']);

        $return = $programProductService->getProducts($program->id);

        $this->assertEquals(static::getSellingPlanProductResponse(), $return);
    }
}
