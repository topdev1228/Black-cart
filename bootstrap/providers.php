<?php

return [
    App\Providers\AppServiceProvider::class,
    App\Providers\RouteServiceProvider::class,
    App\Providers\ViewServiceProvider::class,
    App\Domain\Shared\Providers\UnleashServiceProvider::class,
    App\Domain\Shared\Providers\MetricsServiceProvider::class,
    App\Domain\Shared\Providers\ShopifyGraphqlServiceProvider::class,
    App\Domain\Shared\Providers\GooglePubSubBroadcasterProvider::class,
];
