<?php
declare(strict_types=1);

namespace Feature\Domain\Orders\Listeners;

use App\Domain\Orders\Listeners\WebhookReturnsApproveListener;
use App\Domain\Orders\Values\WebhookReturnsApprove;
use App\Domain\Stores\Models\Store;
use Illuminate\Support\Facades\App;
use Tests\TestCase;

class WebhookReturnsApproveListenerTest extends TestCase
{
    protected Store $currentStore;

    protected function setUp(): void
    {
        parent::setUp();

        $this->currentStore = Store::withoutEvents(function () {
            return Store::factory()->create();
        });
        App::context(store: $this->currentStore);
    }

    public function testItDoesNotApproveReturnNotFoundOrder(): void
    {
        $webhook = WebhookReturnsApprove::from(
            $this->loadFixtureData('order-returns-approve-capture-webhook.json', 'Orders')
        );

        $listener = resolve(WebhookReturnsApproveListener::class);
        $returnValue = $listener->handle($webhook);

        $this->assertNull($returnValue);
        $this->assertDatabaseEmpty('orders_returns');
    }
}
