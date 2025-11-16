<?php
declare(strict_types=1);

namespace Tests\Unit\Domain\Shopify\Services;

use App\Domain\Shared\Services\ShopifyGraphqlService;
use App\Domain\Shopify\Enums\WebhookTopic;
use App\Domain\Shopify\Services\ShopifyWebhookService;
use Str;
use Tests\TestCase;

class ShopifyWebhookServiceTest extends TestCase
{
    public function testItSubscribesToWebhooks(): void
    {
        $topic = WebhookTopic::PRODUCTS_CREATE;

        $shopifyGraphqlService = $this->mock(ShopifyGraphqlService::class, function ($mock) use ($topic) {
            $mock->shouldReceive('postMutation')
                ->once()
                ->withArgs(function ($query, $variables) use ($topic) {
                    $this->assertStringContainsString('mutation pubSubWebhookSubscriptionCreate', $query);
                    $this->assertEquals($topic->value, $variables['topic']);
                    $this->assertEquals('shopify-webhook-products-create', $variables['webhookSubscription']['pubSubTopic']);

                    return true;
                });
        });

        $shopifyWebhookService = new ShopifyWebhookService($shopifyGraphqlService);
        $shopifyWebhookService->subscribe($topic);
    }

    public function testItSubscribeToMultipleTopics(): void
    {
        $topics = [WebhookTopic::PRODUCTS_CREATE, WebhookTopic::PRODUCTS_UPDATE];

        $shopifyGraphqlService = $this->mock(ShopifyGraphqlService::class, function ($mock) use ($topics) {
            foreach ($topics as $topic) {
                $mock->shouldReceive('postMutation')
                    ->once()
                    ->withArgs(function ($query, $variables) use ($topic) {
                        $this->assertStringContainsString('mutation pubSubWebhookSubscriptionCreate', $query);
                        $this->assertEquals($topic->value, $variables['topic']);
                        $this->assertStringContainsString('shopify-webhook-' . Str::of($topic->value)->lower()->replace('_', '-')->toString(), $variables['webhookSubscription']['pubSubTopic']);

                        return true;
                    });
            }
        });

        $shopifyWebhookService = new ShopifyWebhookService($shopifyGraphqlService);
        $shopifyWebhookService->subscribe(...$topics);
    }
}
