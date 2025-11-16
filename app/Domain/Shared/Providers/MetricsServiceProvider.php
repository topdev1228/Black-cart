<?php
declare(strict_types=1);

namespace App\Domain\Shared\Providers;

use App\Domain\Shared\Services\MetricsService;
use DDTrace\GlobalTracer;
use Illuminate\Support\ServiceProvider;

class MetricsServiceProvider extends ServiceProvider
{
    /**
     * Register services.
     */
    public function register(): void
    {
        $this->app->singleton(MetricsService::class, function () {
            if (extension_loaded('ddtrace')) {
                try {
                    return new MetricsService(GlobalTracer::get());
                } catch (\Throwable) {
                }
            }

            // @codeCoverageIgnore
            return new MetricsService(null);
        });

        file_exists(base_path('bootstrap/metrics.php')) && require_once base_path('bootstrap/metrics.php');
    }

    /**
     * Bootstrap services.
     */
    public function boot(): void
    {
    }
}
