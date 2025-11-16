<?php
declare(strict_types=1);

namespace App\Domain\Shared\Providers;

use App\Domain\Shared\Services\UnleashClientService;
use GuzzleHttp\ClientInterface;
use Illuminate\Support\ServiceProvider;
use MikeFrancis\LaravelUnleash\Unleash;

class UnleashServiceProvider extends ServiceProvider
{
    /**
     * Register any application services.
     */
    public function register(): void
    {
        $this->app->when(Unleash::class)->needs(ClientInterface::class)->give(UnleashClientService::class);
    }

    /**
     * Bootstrap any application services.
     */
    public function boot(): void
    {
        //
    }
}
