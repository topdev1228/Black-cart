<?php
declare(strict_types=1);

namespace App\Domain\Shared\Repositories;

use App;
use App\Domain\Shared\Models\ShopifyWebhookData;
use App\Domain\Shopify\Enums\WebhookTopic;

class ShopifyWebhookDataRepository
{
    public function save(WebhookTopic $topic, array $data, array $attributes)
    {
        ShopifyWebhookData::create([
            'store_id' => App::context()->store->id,
            'topic' => $topic,
            'data' => $data,
            'attributes' => $attributes,
        ]);
    }
}
