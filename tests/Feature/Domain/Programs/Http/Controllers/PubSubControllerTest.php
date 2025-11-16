<?php
declare(strict_types=1);

namespace Tests\Feature\Domain\Programs\Http\Controllers;

use App\Domain\Shared\Values\PubSubMessageEnvelope;
use App\Domain\Stores\Events\StoreCreated;
use App\Domain\Stores\Values\Store;
use Illuminate\Support\Collection;
use function json_encode;
use Tests\TestCase;

class PubSubControllerTest extends TestCase
{
    public function testItHandlesEventPubSubMessages(): void
    {
        $this->app->singleton('App\Domain\Tests\Listeners\MockListener', fn () => new class($this) {
            public function __construct(protected PubSubControllerTest $test)
            {
            }

            public function handle(): void
            {
                $this->test->assertTrue(true);
            }
        });

        $event = new StoreCreated(Store::builder()->create());
        $message = PubSubMessageEnvelope::builder()->create([
            'subscription' => 'shopify-tests-mock',
        ])->toArray();
        $message['message']['data'] = base64_encode(json_encode($event));

        $response = $this->postJson('/pubsub/stores/programs', $message);
        $response->assertOk();
    }

    public function testItHandlesShopifyPubSubMessages(): void
    {
        $this->app->singleton('App\Domain\Tests\Listeners\MockListener', fn () => new class($this) {
            public function __construct(protected PubSubControllerTest $test)
            {
            }

            public function handle(Collection $data): void
            {
                $this->test->assertCount(18, $data);
            }
        });

        $message = PubSubMessageEnvelope::builder([
            'subscription' => 'shopify-tests-mock',
        ])->shopifyWebhook()->create()->toArray();
        $message['message']['data'] = base64_encode(json_encode($message['message']['data']));

        $response = $this->postJson('/pubsub/stores/programs', $message);
        $response->assertOk();
    }
}
