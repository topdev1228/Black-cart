<?php
declare(strict_types=1);

namespace Tests\Unit\Domain\Shared\Broadcasting\Broadcasters;

use App;
use App\Domain\Shared\Broadcasting\Broadcasters\GooglePubSubBroadcaster;
use App\Domain\Shared\Exceptions\UnknownPubSubTopicException;
use App\Domain\Stores\Values\Store;
use Google\Cloud\PubSub\PubSubClient;
use Google\Cloud\PubSub\Topic;
use Illuminate\Broadcasting\Channel;
use Illuminate\Support\Facades\Date;
use Mockery\MockInterface;
use Str;
use Tests\TestCase;

class GooglePubSubBroadcasterTest extends TestCase
{
    public function testItBroadcastsToTopic(): void
    {
        $uuid = Str::uuid();

        Str::createUuidsUsing(function () use ($uuid) {
            return $uuid;
        });

        App::context(store: Store::builder()->create());

        $this->mock(PubSubClient::class, function (MockInterface $mock) use ($uuid) {
            $mock->shouldReceive('topic')
                ->once()
                ->andReturn($this->mock(Topic::class, function (MockInterface $mock) use ($uuid) {
                    $mock->shouldReceive('exists')
                        ->once()
                        ->andReturnTrue();

                    $mock->shouldReceive('name')
                        ->once()
                        ->andReturn('shopify-test-topic');

                    $mock->shouldReceive('publish')
                        ->withArgs([
                            [
                                'data' => json_encode(['foo' => 'bar'], JSON_THROW_ON_ERROR | JSON_UNESCAPED_UNICODE),
                                'attributes' => [
                                    'uuid' => (string) $uuid,
                                    'event' => 'test-event',
                                    'published_at' => Date::now()->format('Y-m-d\TH:i:sP'),
                                    'domain' => App::context()->store->domain,
                                ],
                            ],
                        ])
                        ->once();
                }));
        });

        $broadcaster = resolve(GooglePubSubBroadcaster::class);
        $broadcaster->broadcast(
            [
                new Channel('\\App\\Domain\\Tests\\Events\\TestTopicEvent'),
            ],
            'test-event',
            ['foo' => 'bar']
        );
    }

    public function testItFailsOnUnknownTopic(): void
    {
        $this->mock(PubSubClient::class, function (MockInterface $mock) {
            $mock->shouldReceive('topic')
                ->once()
                ->andReturn($this->mock(Topic::class, function (MockInterface $mock) {
                    $mock->shouldReceive('exists')
                        ->once()
                        ->andReturnFalse();
                }));
        });

        $this->expectException(UnknownPubSubTopicException::class);

        $broadcaster = resolve(GooglePubSubBroadcaster::class);
        $broadcaster->broadcast(
            [
                new Channel(static::class),
            ],
            'test-event',
        );
    }
}
