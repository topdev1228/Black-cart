<?php
declare(strict_types=1);

namespace Tests\Feature\Domain\Shopify\Console\Commands;

use App\Domain\Shopify\Console\Commands\SubscribeWebhooksForActiveStores;
use App\Domain\Shopify\Services\WebhooksService;
use App\Domain\Stores\Models\Store;
use App\Domain\Stores\Models\StoreSetting;
use App\Domain\Stores\Repositories\InternalStoreRepository;
use Illuminate\Support\Facades\Http;
use Tests\TestCase;

class SubscribeWebhooksForActiveStoresTest extends TestCase
{
    public function testItSubscribesWebhooksForActiveStores(): void
    {
        $numStores = 5;
        $numWebhooks = 10;
        $stores = Store::withoutEvents(function () use ($numStores) {
            return Store::factory()->count($numStores)->create();
        });
        foreach ($stores as $store) {
            StoreSetting::withoutEvents(function () use ($store) {
                StoreSetting::factory()->for($store)->secure()->create([
                    'name' => 'shopify_oauth_token',
                    'value' => 'token_' . $store->id,
                ]);
            });
        }

        $httpCalls = [];
        foreach ($stores as $store) {
            $httpCalls[$store->domain . '/admin/api/*/graphql.json'] = Http::response($this->graphqlResponse());
        }
        Http::fake($httpCalls);

        $webhookService = resolve(WebhooksService::class);
        $internalStoreRepository = resolve(InternalStoreRepository::class);

        $command = resolve(SubscribeWebhooksForActiveStores::class);
        $command->handle($webhookService, $internalStoreRepository);

        Http::assertSentCount($numStores * $numWebhooks);
    }

    public function testItDoesNotSubscribeWebhooksForActiveStoresWithoutShopifyAccessToken(): void
    {
        Store::withoutEvents(function () {
            return Store::factory()->count(2)->create();
        });

        Http::fake();

        $webhookService = resolve(WebhooksService::class);
        $internalStoreRepository = resolve(InternalStoreRepository::class);

        $command = resolve(SubscribeWebhooksForActiveStores::class);
        $command->handle($webhookService, $internalStoreRepository);

        Http::assertNothingSent();
    }

    public function testItDoesNotSubscribeWebhooksForInactiveStores(): void
    {
        Store::withoutEvents(function () {
            return Store::factory()->count(2)->create([
                'deleted_at' => now(),
            ]);
        });

        Http::fake();

        $webhookService = resolve(WebhooksService::class);
        $internalStoreRepository = resolve(InternalStoreRepository::class);

        $command = resolve(SubscribeWebhooksForActiveStores::class);
        $command->handle($webhookService, $internalStoreRepository);

        Http::assertNothingSent();
    }

    private function graphqlResponse(): array
    {
        return  [
            'data' => [
                'pubSubWebhookSubscriptionCreate' => [
                    'webhookSubscription' => [
                        'id' => 'gid =>//shopify/WebhookSubscription/4383802720',
                        'topic' => 'ORDERS_CREATE',
                        'format' => 'JSON',
                        'endpoint' => [
                            '__typename' => 'WebhookPubSubEndpoint',
                            'pubSubProject' => 'my-gcp-project',
                            'pubSubTopic' => 'my-gcp-topic',
                        ],
                    ],
                ],
            ],
        ];
    }
}
