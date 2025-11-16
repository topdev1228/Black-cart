<?php
declare(strict_types=1);

namespace Tests\Unit\Domain\Orders\Services;

use App;
use App\Domain\Orders\Models\LineItem as LineItemModel;
use App\Domain\Orders\Models\Order as OrderModel;
use App\Domain\Orders\Services\TrialService;
use App\Domain\Orders\Values\LineItem as LineItemValue;
use App\Domain\Stores\Models\Store;
use App\Domain\Trials\Values\Trialable;
use Illuminate\Support\Facades\Http;
use Str;
use Tests\TestCase;

class TrialServiceTest extends TestCase
{
    protected TrialService $trialService;
    protected Store $store;

    protected function setUp(): void
    {
        parent::setUp();
        $this->trialService = resolve(TrialService::class);
        $this->store = Store::factory()->create();
        App::context(store: $this->store);
    }

    public function testInitiateTrial(): void
    {
        $order = OrderModel::withoutEvents(function () {
            return OrderModel::factory()->create([
                'store_id' => $this->store->id,
            ]);
        });
        $lineItem = LineItemModel::factory()->create([
            'order_id' => $order,
        ]);

        $trialResponse = Trialable::builder()->create([
            'id' => (string) Str::uuid(),
            'groupKey' => (string) Str::uuid(),
            'sourceId' => $lineItem->id,
        ]);

        Http::fake([
            'http://localhost:8080/api/trials' => Http::response([
                'trial' => $trialResponse,
            ], 201),
        ]);

        $this->trialService->initiateTrial(LineItemValue::from($lineItem));

        $this->assertNotNull($lineItem->refresh()->trialable_id);
        $this->assertNotNull($lineItem->refresh()->trial_group_id);
    }
}
