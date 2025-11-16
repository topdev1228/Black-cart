<?php
declare(strict_types=1);

namespace Tests\Unit\Domain\Shopify\Repositories;

use App;
use App\Domain\Shopify\Enums\MandatoryWebhookTopic;
use App\Domain\Shopify\Repositories\MandatoryWebhookRepository;
use App\Domain\Shopify\Values\MandatoryWebhook as MandatoryWebhookValue;
use App\Domain\Stores\Models\Store;
use Tests\TestCase;

class MandatoryWebhookRepositoryTest extends TestCase
{
    protected Store $currentStore;
    protected MandatoryWebhookRepository $mandatoryWebhookRepository;

    protected function setUp(): void
    {
        parent::setUp();

        $this->currentStore = Store::withoutEvents(function () {
            return Store::factory()->create();
        });
        App::context(store: $this->currentStore);

        $this->mandatoryWebhookRepository = resolve(MandatoryWebhookRepository::class);
    }

    public function testItCreatesMandatoryWebhookForCustomersDataRequest(): void
    {
        $mandatoryWebhookValue = MandatoryWebhookValue::builder()->customersDataRequest()->create([
            'store_id' => $this->currentStore->id,
            'topic' => MandatoryWebhookTopic::CUSTOMERS_DATA_REQUEST,
        ]);

        $actual = $this->mandatoryWebhookRepository->store($mandatoryWebhookValue);

        $this->validate($mandatoryWebhookValue, $actual);
    }

    public function testItCreatesMandatoryWebhookForCustomersRedact(): void
    {
        $mandatoryWebhookValue = MandatoryWebhookValue::builder()->customersRedact()->create([
            'store_id' => $this->currentStore->id,
            'topic' => MandatoryWebhookTopic::CUSTOMERS_REDACT,
        ]);

        $actual = $this->mandatoryWebhookRepository->store($mandatoryWebhookValue);

        $this->validate($mandatoryWebhookValue, $actual);
    }

    public function testItCreatesMandatoryWebhookForShopRedact(): void
    {
        $mandatoryWebhookValue = MandatoryWebhookValue::builder()->create([
            'store_id' => $this->currentStore->id,
            'topic' => MandatoryWebhookTopic::SHOP_REDACT,
        ]);

        $actual = $this->mandatoryWebhookRepository->store($mandatoryWebhookValue);

        $this->validate($mandatoryWebhookValue, $actual);
    }

    private function validate(MandatoryWebhookValue $expected, MandatoryWebhookValue $actual): void
    {
        $this->assertNotEmpty($actual->id);
        $this->assertEquals($expected->storeId, $actual->storeId);
        $this->assertEquals($expected->topic, $actual->topic);
        $this->assertEquals($expected->shopifyShopId, $actual->shopifyShopId);
        $this->assertEquals($expected->shopifyDomain, $actual->shopifyDomain);
        $this->assertEquals($expected->data, $actual->data);
        $this->assertEquals($expected->status, $actual->status);

        $expected->id = $actual->id;
        $dbExpected = $expected->toArray();
        $dbExpected['data'] = json_encode($dbExpected['data']);

        $this->assertDatabaseHas('shopify_mandatory_webhooks', $dbExpected);
    }
}
