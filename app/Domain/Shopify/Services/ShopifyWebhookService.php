<?php
declare(strict_types=1);

namespace App\Domain\Shopify\Services;

use App\Domain\Shared\Services\ShopifyGraphqlService;
use App\Domain\Shopify\Enums\WebhookTopic;
use Str;

class ShopifyWebhookService
{
    public function __construct(protected ShopifyGraphqlService $shopifyGraphqlService)
    {
    }

    public function subscribe(WebhookTopic ...$topics): void
    {
        $mutation = 'pubSubWebhookSubscriptionCreate';

        $query = <<<'QUERY'
            mutation pubSubWebhookSubscriptionCreate($topic: WebhookSubscriptionTopic!, $webhookSubscription: PubSubWebhookSubscriptionInput!) {
              pubSubWebhookSubscriptionCreate(topic: $topic, webhookSubscription: $webhookSubscription) {
                webhookSubscription {
                  id
                  topic
                  format
                  endpoint {
                    __typename
                    ... on WebhookPubSubEndpoint {
                      pubSubProject
                      pubSubTopic
                    }
                  }
                }
              }
            }
        QUERY;

        $variables = [
            'webhookSubscription' => [
                'pubSubProject' => config('services.shopify.pubsub.project'),
            ],
        ];

        foreach ($topics as $topic) {
            $variables['topic'] = $topic->value;
            $variables['webhookSubscription']['pubSubTopic'] = Str::of($topic->value)->lower()->replace('_', '-')->prepend('shopify-webhook-')->toString();

            $this->shopifyGraphqlService->postMutation($query, $variables);
        }
    }
}
