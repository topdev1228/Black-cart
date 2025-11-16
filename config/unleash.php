<?php
declare(strict_types=1);

use App\Flags\StoreWithDomainsStrategy;
use MikeFrancis\LaravelUnleash\Strategies\ApplicationHostnameStrategy;
use MikeFrancis\LaravelUnleash\Strategies\DefaultStrategy;
use MikeFrancis\LaravelUnleash\Strategies\RemoteAddressStrategy;
use MikeFrancis\LaravelUnleash\Strategies\UserWithIdStrategy;

return [
  // URL of the Unleash server.
  // This should be the base URL, do not include /api or anything else.
  'url' => env('UNLEASH_URL'),

  // Globally control whether Unleash is enabled or disabled.
  // If not enabled, no API requests will be made and all "enabled" checks will return `false` and
  // "disabled" checks will return `true`.
  'isEnabled' => env('UNLEASH_ENABLED', true),

  // Allow the Unleash API response to be cached.
  // Default TTL is 15s
  // Failover caching will use the last successful result from Unleash if it down.
  // Failover is independent of regular caching.
  'cache' => [
    'isEnabled' => env('UNLEASH_CACHE_ENABLED', true),
    'ttl' => env('UNLEASH_CACHE_TTL', 15),
    'failover' => env('UNLEASH_CACHE_FAILOVER', true),
  ],

  'apiKey' => env('UNLEASH_API_KEY'),

  // Mapping of strategies used to guard features on Unleash. The default strategies are already
  // mapped below, and more strategies can be added - they just need to implement the
  // `\MikeFrancis\LaravelUnleash\Strategies\Strategy` or
  // `\MikeFrancis\LaravelUnleash\Strategies\DynamicStrategy` interface. If you would like to disable
  // a built-in strategy, please comment it out or remove it below.
  'strategies' => [
    'applicationHostname' => ApplicationHostnameStrategy::class,
    'default' => DefaultStrategy::class,
    'remoteAddress' => RemoteAddressStrategy::class,
    'userWithIds' => UserWithIdStrategy::class,
    'StoreWithDomains' => StoreWithDomainsStrategy::class,
  ],
];
