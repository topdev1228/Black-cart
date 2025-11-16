<?php

declare(strict_types=1);

namespace Tests\Unit\Domain\Shared\Repositories;

use App;
use App\Domain\Shared\Repositories\ShopifyWebhookDataRepository;
use App\Domain\Shopify\Enums\WebhookTopic;
use App\Domain\Stores\Models\Store;
use Tests\TestCase;

class ShopifyWebhookDataRepositoryTest extends TestCase
{
    public function testItSaves(): void
    {
        $repository = new ShopifyWebhookDataRepository();

        App::context(store: Store::factory()->create());

        $topic = WebhookTopic::PRODUCTS_CREATE;
        $data = ['test' => 'data'];
        $attributes = ['X-Shopify-Shop-Domain' => 'test.myshopify.com'];

        $repository->save($topic, $data, $attributes);

        $this->assertDatabaseHas('shopify_webhook_data', [
            'store_id' => App::context()->store->id,
            'topic' => $topic->value,
            'data' => json_encode($data),
            'attributes' => json_encode($attributes),
        ]);
    }
}
