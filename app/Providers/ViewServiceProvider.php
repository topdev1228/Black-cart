<?php
declare(strict_types=1);

namespace App\Providers;

use Illuminate\Support\ServiceProvider;
use Illuminate\Support\Str;

class ViewServiceProvider extends ServiceProvider
{
    public function boot(): void
    {
        $this->registerDomainViewFolders();
    }

    protected function registerDomainViewFolders(): void
    {
        foreach (glob(app_path('Domain') . '/*/resources/views') as $path) {
            $domain = Str::of($path)
                ->after(app_path('Domain/'))
                ->before('/resources/views')
                ->toString();

            $this->loadViewsFrom($path, $domain);
        }
    }
}
