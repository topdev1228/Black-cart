<?php
declare(strict_types=1);

namespace Tests\Unit\Domain\Payments\Services;

use App;
use App\Domain\Payments\Services\ShopifyOrderService;
use App\Domain\Shared\Exceptions\Shopify\ShopifyMutationServerException;
use App\Domain\Shared\Services\ShopifyGraphqlService;
use App\Domain\Stores\Values\Store as StoreValue;
use Mockery\MockInterface;
use Tests\TestCase;

class ShopifyOrderServiceTest extends TestCase
{
    public function testItGetsArchiveStatusClosed(): void
    {
        App::context(store: StoreValue::builder()->create());

        $this->mock(ShopifyGraphqlService::class, function (MockInterface $mock) {
            $mock->shouldReceive('post')
                ->once()
                ->andReturn(['data' => ['order' => ['closed' => true]]]);
        });

        $shopifyOrderService = resolve(ShopifyOrderService::class);
        $this->assertTrue($shopifyOrderService->isOrderArchived('gid://shopify/Order/123456789'));
    }

    public function testItGetsArchiveStatusOpen(): void
    {
        App::context(store: StoreValue::builder()->create());

        $this->mock(ShopifyGraphqlService::class, function (MockInterface $mock) {
            $mock->shouldReceive('post')
                ->once()
                ->andReturn(['data' => ['order' => ['closed' => false]]]);
        });

        $shopifyOrderService = resolve(ShopifyOrderService::class);
        $this->assertFalse($shopifyOrderService->isOrderArchived('gid://shopify/Order/123456789'));
    }

    public function testItOpensOrder(): void
    {
        App::context(store: StoreValue::builder()->create());

        $this->mock(ShopifyGraphqlService::class, function (MockInterface $mock) {
            $mock->shouldReceive('postMutation')
                ->once();
        });

        $shopifyOrderService = resolve(ShopifyOrderService::class);
        $this->assertTrue($shopifyOrderService->openOrder('gid://shopify/Order/123456789'));
    }

    public function testItFailsToOpenOrder(): void
    {
        App::context(store: StoreValue::builder()->create());

        $this->mock(ShopifyGraphqlService::class, function (MockInterface $mock) {
            $mock->shouldReceive('postMutation')
                ->once()
                ->andThrow(new ShopifyMutationServerException('orderOpen', 'An error occurred'));
        });

        $this->expectException(ShopifyMutationServerException::class);

        $shopifyOrderService = resolve(ShopifyOrderService::class);
        $shopifyOrderService->openOrder('gid://shopify/Order/123456789');
    }
}
