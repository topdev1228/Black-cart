<?php
declare(strict_types=1);

namespace App\Domain\Shared\Http\Client;

use App;
use Closure;
use Illuminate\Support\Facades\Http;
use Str;
use Swis\Laravel\Bridge\PsrHttpClient\Client;

class ShopifyHttpClient extends Client
{
    public function __construct(?string $version = null)
    {
        $version ??= config('services.shopify.admin_graphql_api_version');

        parent::__construct($this->getRequestFactory($version));
    }

    public function setVersion(string $version): self
    {
        $this->pendingRequestFactory = $this->getRequestFactory($version);

        return $this;
    }

    protected function getRequestFactory(string $version): Closure
    {
        $store = App::context()->store;
        $uri = Str::of($store->domain)
            ->start('https://')
            ->finish(sprintf(
                '/admin/api/%s/graphql.json',
                $version
            ))
            ->toString();

        return fn () => Http::baseUrl($uri)->withHeader('X-Shopify-Access-Token', $store->accessToken);
    }
}
