<?php
declare(strict_types=1);

namespace App\Domain\Shared\Providers;

use App\Domain\Shared\Services\ShopifyGraphqlService;
use Illuminate\Support\ServiceProvider;

class ShopifyGraphqlServiceProvider extends ServiceProvider
{
    /**
     * Register services.
     */
    public function register(): void
    {
        $this->app->bind(ShopifyGraphqlService::class, function () {
            return new ShopifyGraphqlService(
                config('services.shopify.admin_graphql_api_version'),
            );
        });
    }

    /**
     * Bootstrap services.
     */
    public function boot(): void
    {
    }
}
