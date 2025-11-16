<?php
declare(strict_types=1);

namespace Tests\Unit\Domain\Orders\Repositories;

use App\Domain\Orders\Enums\LineItemStatus;
use App\Domain\Orders\Models\LineItem as LineItemModel;
use App\Domain\Orders\Repositories\LineItemRepository;
use App\Domain\Orders\Values\LineItem as LineItemValue;
use Illuminate\Database\Eloquent\ModelNotFoundException;
use Tests\TestCase;

class LineItemRepositoryTest extends TestCase
{
    protected LineItemRepository $lineItemRepository;

    protected function setUp(): void
    {
        parent::setUp();
        $this->lineItemRepository = resolve(LineItemRepository::class);
    }

    public function testGetById(): void
    {
        $lineItemModel = LineItemModel::factory()->create();
        $response = $this->lineItemRepository->getById($lineItemModel->id);

        $this->assertEquals(LineItemValue::from($lineItemModel), $response);
    }

    public function testGetByIdNotFound(): void
    {
        $this->expectException(ModelNotFoundException::class);
        $this->lineItemRepository->getById('1234');
    }

    public function testItGetsLineItemByTrialGroupId(): void
    {
        $lineItem = LineItemModel::factory(['trial_group_id' => 'trial-group-id'])->create();
        $lineItemRepository = new LineItemRepository();

        $result = $lineItemRepository->getByTrialGroupId($lineItem->trial_group_id);

        $this->assertEquals(LineItemValue::from($lineItem), $result);
    }

    public function testItThrowsExceptionWhenGettingLineItemByNonExistingTrialGroupId(): void
    {
        $this->expectException(ModelNotFoundException::class);

        $nonExistingTrialGroupId = 'non-existing-trial-group-id';
        $this->lineItemRepository->getByTrialGroupId($nonExistingTrialGroupId);
    }

    public function testUpdate(): void
    {
        $lineItemModel = LineItemModel::factory()->create();
        $lineItemValue = LineItemValue::from($lineItemModel);
        $lineItemValue->status = LineItemStatus::ARCHIVED;

        $this->lineItemRepository->update($lineItemValue);

        $this->assertEquals(LineItemStatus::ARCHIVED, $lineItemModel->refresh()->status);
    }

    public function testUpdateNoop(): void
    {
        $lineItemModel = LineItemModel::factory()->create();
        $lineItemValue = LineItemValue::from($lineItemModel);
        $this->lineItemRepository->update($lineItemValue);

        $this->assertEquals($lineItemModel, $lineItemModel->refresh());
    }

    public function testCreate(): void
    {
        $lineItemValue = LineItemValue::builder()->create();

        $response = $this->lineItemRepository->create($lineItemValue);
        $this->assertNotNull($response->id);
        $this->assertDatabaseCount('orders_line_items', 1);
    }

    public function testGetAllEmpty(): void
    {
        $response = $this->lineItemRepository->all();
        $this->assertEquals(0, $response->count());
    }

    public function testGetAll(): void
    {
        $numToCreate = 4;

        LineItemModel::factory()->count($numToCreate)->create();
        $response = $this->lineItemRepository->all();
        $this->assertEquals($numToCreate, $response->count());
    }
}
