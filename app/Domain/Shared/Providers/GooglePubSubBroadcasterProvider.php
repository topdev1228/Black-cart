<?php
declare(strict_types=1);

namespace App\Domain\Shared\Providers;

use App\Domain\Shared\Broadcasting\Broadcasters\GooglePubSubBroadcaster;
use Google\Cloud\PubSub\PubSubClient;
use Illuminate\Broadcasting\BroadcastManager;
use Illuminate\Support\ServiceProvider;

class GooglePubSubBroadcasterProvider extends ServiceProvider
{
    public function boot(BroadcastManager $broadcastManager): void
    {
        $broadcastManager->extend('pubsub', function () {
            $client = new PubSubClient([
                'projectId' => config('broadcasting.connections.pubsub.project_id'),
            ]);

            return new GooglePubSubBroadcaster($client);
        });
    }
}
