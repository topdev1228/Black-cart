<?php
declare(strict_types=1);

namespace Tests\Unit\Domain\Orders\Repositories;

use App\Domain\Orders\Models\Order as OrderModel;
use App\Domain\Orders\Models\TrialGroup as TrialGroupModel;
use App\Domain\Orders\Repositories\TrialGroupRepository;
use App\Domain\Orders\Values\Order as OrderValue;
use App\Domain\Orders\Values\TrialGroup as TrialGroupValue;
use Illuminate\Database\Eloquent\ModelNotFoundException;
use Tests\TestCase;

class TrialGroupRepositoryTest extends TestCase
{
    protected TrialGroupRepository $trialGroupRepository;

    protected function setUp(): void
    {
        parent::setUp();
        $this->trialGroupRepository = resolve(TrialGroupRepository::class);
    }

    public function testGetByOrderEmpty(): void
    {
        $orderModel = OrderModel::withoutEvents(function () {
            return OrderModel::factory()->create();
        });
        $this->expectException(ModelNotFoundException::class);
        $this->trialGroupRepository->getByOrder(OrderValue::from($orderModel));
    }

    public function testGetByOrder(): void
    {
        $orderModel = OrderModel::withoutEvents(function () {
            return OrderModel::factory()->create();
        });
        $groupModel = TrialGroupModel::factory()->create([
            'order_id' => $orderModel->id,
        ]);

        $group = $this->trialGroupRepository->getByOrder(OrderValue::from($orderModel));
        $this->assertEquals(TrialGroupValue::from($groupModel), $group);
    }

    public function testCreateForOrder(): void
    {
        $orderModel = OrderModel::withoutEvents(function () {
            return OrderModel::factory()->create();
        });
        $this->trialGroupRepository->createForOrder(OrderValue::from($orderModel));

        $this->assertDatabaseCount('orders_trial_group', 1);
    }

    public function testCreateForOrderNotIdempotent(): void
    {
        $orderModel = OrderModel::withoutEvents(function () {
            return OrderModel::factory()->create();
        });
        $this->trialGroupRepository->createForOrder(OrderValue::from($orderModel));
        $this->trialGroupRepository->createForOrder(OrderValue::from($orderModel));
        $this->trialGroupRepository->createForOrder(OrderValue::from($orderModel));

        $this->assertDatabaseCount('orders_trial_group', 3);
    }
}
