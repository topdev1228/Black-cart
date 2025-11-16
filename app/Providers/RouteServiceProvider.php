<?php
declare(strict_types=1);

namespace App\Providers;

use function file_exists;
use Illuminate\Foundation\Support\Providers\RouteServiceProvider as ServiceProvider;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\Route;
use Illuminate\Support\Str;

class RouteServiceProvider extends ServiceProvider
{
    /**
     * The path to your application's "home" route.
     *
     * Typically, users are redirected here after authentication.
     *
     * @var string
     */
    public const HOME = '/';

    /**
     * Define your route model bindings, pattern filters, and other route configuration.
     */
    public function boot(): void
    {
        $this->registerGlobalRoutes();
        $this->registerDomainApiRoutes();
        $this->registerDomainWebRoutes();
        $this->registerDomainPubSubRoutes();

        Route::macro('domains', function (?string $domain = null): Collection|bool {
            if ($domain === 'localhost') {
                return true;
            }

            $domains = [config('app.url') => true];
            /** @var \Illuminate\Routing\Route $route */
            foreach (Route::getRoutes()->getRoutes() as $route) {
                $routeDomain = $route->getDomain();
                if ($routeDomain === null) {
                    continue;
                }

                if ($domain !== null) {
                    if ($routeDomain === $domain) {
                        return true;
                    }
                    continue;
                }

                $domains[$routeDomain] = true;
            }

            if ($domain !== null) {
                return false;
            }

            return collect(array_keys($domains));
        });
    }

    protected function registerGlobalRoutes(): void
    {
        $this->routes(function () {
            if (file_exists(base_path('routes/api.php'))) {
                Route::middleware('api')
                    ->prefix('api')
                    ->group(base_path('routes/api.php'));
            }

            if (file_exists(base_path('routes/web.php'))) {
                Route::middleware('web')
                    ->group(base_path('routes/web.php'));
            }
        });
    }

    protected function registerDomainApiRoutes(): void
    {
        foreach (glob(app_path('Domain') . '/*/routes/api.php') as $path) {
            $domain = Str::of($path)
                ->after(app_path('Domain/'))
                ->before('/routes/api.php')
                ->lower();

            $name = $domain->append('.api.')
                ->toString();

            $key = Str::of('services.domains.')->append($domain->toString(), '.hostname')->toString();

            Route::middleware('api')
                ->name($name)
                ->prefix('api')
                ->domain(config($key))
                ->group($path);
        }
    }

    protected function registerDomainWebRoutes(): void
    {
        foreach (glob(app_path('Domain') . '/*/routes/web.php') as $path) {
            $domain = Str::of($path)
                ->after(app_path('Domain/'))
                ->before('/routes/web.php')
                ->lower()
                ->append('.web.')
                ->toString();
            Route::middleware('web')->prefix('/')->name($domain)->group($path);
        }
    }

    protected function registerDomainPubSubRoutes(): void
    {
        foreach (glob(app_path('Domain') . '/*/routes/pubsub.php') as $path) {
            $domain = Str::of($path)
                ->after(app_path('Domain/'))
                ->before('/routes/pubsub.php')
                ->lower()
                ->append('.pubsub.')
                ->toString();
            Route::middleware('pubsub')->prefix('pubsub')->name($domain)->group($path);
        }
    }
}
