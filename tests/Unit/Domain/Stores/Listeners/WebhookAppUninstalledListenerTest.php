<?php

declare(strict_types=1);

namespace Tests\Unit\Domain\Stores\Listeners;

use App;
use App\Domain\Shared\Values\PubSubMessage;
use App\Domain\Stores\Listeners\WebhookAppUninstalledListener;
use App\Domain\Stores\Services\StoreService;
use Tests\TestCase;

class WebhookAppUninstalledListenerTest extends TestCase
{
    public function testHandle(): void
    {
        $storeService = $this->mock(StoreService::class);
        $storeService
            ->shouldReceive('delete')
            ->once()
            ->with(App::context()->store);

        $listener = new WebhookAppUninstalledListener($storeService);

        $pubSubMessage = PubSubMessage::builder()->create();

        $listener->handle($pubSubMessage);
    }
}
