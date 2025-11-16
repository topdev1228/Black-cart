<?php
declare(strict_types=1);

namespace App\Domain\Shared\Broadcasting\Broadcasters;

use App;
use App\Domain\Shared\Exceptions\UnknownPubSubTopicException;
use App\Domain\Shared\Facades\AppMetrics;
use App\Domain\Shared\Services\MetricsService;
use Google\Cloud\PubSub\PubSubClient;
use Google\Cloud\PubSub\Topic;
use Illuminate\Broadcasting\Channel;
use Illuminate\Contracts\Broadcasting\Broadcaster;
use Illuminate\Support\Facades\Date;
use function json_encode;
use const JSON_UNESCAPED_UNICODE;
use Str;

class GooglePubSubBroadcaster implements Broadcaster
{
    public function __construct(protected PubSubClient $client)
    {
    }

    /**
     * @psalm-param array<Channel> $channels
     * @psalm-param string $event
     *
     * @psalm-suppress MoreSpecificImplementedParamType
     */
    public function broadcast(array $channels, $event, array $payload = []): void
    {
        foreach ($channels as $channel) {
            $topic = $this->getTopic($channel);

            AppMetrics::trace('broadcast', function (MetricsService $metrics) use ($event, $payload, $topic) {
                $uuid = (string) Str::uuid();
                $publishedAt = Date::now()->format('Y-m-d\TH:i:sP');

                $metrics->setTag('uuid', $uuid);
                $metrics->setTag('event', ['name' => $event, 'topic' => $topic->name()]);
                $metrics->setTag('published_at', $publishedAt);

                $topic->publish([
                    'data' => json_encode($payload, JSON_THROW_ON_ERROR | JSON_UNESCAPED_UNICODE),
                    'attributes' => [
                        'uuid' => $uuid,
                        'event' => $event,
                        'published_at' => $publishedAt,
                        'domain' => App::context()->store->domain,
                    ],
                ]);
            });
        }
    }

    protected function getTopic(Channel $channel): Topic
    {
        $topicName = $this->getTopicName($channel);

        $topic = $this->client->topic($topicName, ['encode' => false]);

        if (!$topic->exists()) {
            throw new UnknownPubSubTopicException($topicName);
        }

        return $topic;
    }

    public function auth($request): void
    {
        // Intentionally left blank
    }

    public function validAuthenticationResponse($request, $result): void
    {
        // Intentionally left blank
    }

    /**
     * Transform class name to topic name
     *
     * Topic name Format:
     * shopify-{domain}-{event-name}
     *
     * Example:
     * \App\Domain\Stores\Events\StoreCreated => shopify-stores-store-created
     */
    protected function getTopicName(Channel $channel): string
    {
        $name = Str::of($channel->name);
        $domain = $name->betweenFirst('Domain\\', '\\');
        $event = $name->classBasename()->replaceEnd('Event', '')->kebab();

        return Str::of('shopify')->append('-', $domain, '-', $event)->lower()->toString();
    }
}
