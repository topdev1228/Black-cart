<?php
declare(strict_types=1);

namespace App\Domain\Shared\Http\Middleware;

use App\Domain\Stores\Repositories\StoreRepository;
use Closure;
use Illuminate\Http\Request;

class EnsureShopifyInstalled
{
    /**
     * Checks if the shop in the query arguments is currently installed.
     */
    public function handle(Request $request, Closure $next)
    {
        $shop = $request->query('shop') ? $request->query('shop') : null;

        $storeRepository = resolve(StoreRepository::class);
        $appInstalled = false;
        if ($shop) {
            try {
                $storeRepository->getByDomain($shop);
                $appInstalled = true;
            } catch(\Exception) {
            }
        }
        $isExitingIframe = preg_match('/^ExitIframe/i', $request->path());

        return ($appInstalled || $isExitingIframe) ? $next($request) : redirect(route('shopify.web.redirect', ['shop' => $shop]));
    }
}
