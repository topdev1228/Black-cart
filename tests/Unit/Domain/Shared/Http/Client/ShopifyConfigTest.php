<?php
declare(strict_types=1);

namespace Tests\Unit\Domain\Shared\Http\Client;

use App\Domain\Shared\Http\Client\ShopifyConfig;
use App\Domain\Stores\Models\Store;
use Http;
use Illuminate\Support\Facades\App;
use Illuminate\Support\Str;
use Tests\TestCase;

class ShopifyConfigTest extends TestCase
{
    public function testItGeneratesStableConfig(): void
    {
        App::context(store: Store::factory(['domain' => 'example.myshopify.com'])->create());

        $config = new ShopifyConfig('Orders', '2024-01', true);

        $client = $config->makeClient();

        Http::fake([
            'https://example.myshopify.com/admin/api/2024-01/graphql.json/' => Http::response(['data' => ['orders' => []]]),
        ]);

        $client->request('query { orders { id } }');

        Http::assertSentCount(1);

        $this->assertEquals('App\Domain\Orders\GraphQL\Shopify\Stable', $config->namespace());
        $this->assertTrue(Str::endsWith($config->schemaPath(), '/graphql/shopify-2024-01.graphql'));
        $this->assertTrue(Str::endsWith($config->searchPath(), '/app/Domain/Orders/GraphQL/Queries/Shopify/2024-01'));
        $this->assertTrue(Str::endsWith($config->targetPath(), '/app/Domain/Orders/GraphQL/Shopify/Stable'));
    }

    public function testItGeneratesUnstableConfig(): void
    {
        App::context(store: Store::factory(['domain' => 'example.myshopify.com'])->create());

        $config = new ShopifyConfig('Orders', 'unstable', false);

        $client = $config->makeClient();

        Http::fake([
            'https://example.myshopify.com/admin/api/unstable/graphql.json/' => Http::response(['data' => ['orders' => []]]),
        ]);

        $client->request('query { orders { id } }');

        Http::assertSentCount(1);

        $this->assertEquals('App\Domain\Orders\GraphQL\Shopify\Unstable', $config->namespace());
        $this->assertTrue(Str::endsWith($config->schemaPath(), '/graphql/shopify-unstable.graphql'));
        $this->assertTrue(Str::endsWith($config->searchPath(), '/app/Domain/Orders/GraphQL/Queries/Shopify/Unstable'));
        $this->assertTrue(Str::endsWith($config->targetPath(), '/app/Domain/Orders/GraphQL/Shopify/Unstable'));
    }
}
