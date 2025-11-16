<?php
declare(strict_types=1);

namespace App\Domain\Shared\Http\Middleware;

use App\Domain\Stores\Repositories\StoreRepository;
use Closure;
use Illuminate\Http\Request;

class EnsureShopifySession
{
    public const ACCESS_MODE_ONLINE = 'online';
    public const ACCESS_MODE_OFFLINE = 'offline';

    public const TEST_GRAPHQL_QUERY = <<<'QUERY'
    {
        shop {
            name
        }
    }
    QUERY;

    /**
     * Checks if there is currently an active Shopify session.
     */
    public function handle(Request $request, Closure $next, string $accessMode = self::ACCESS_MODE_OFFLINE)
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
        // if shop authenticated
        if ($request->has('embedded') && $request->get('embedded') === '1' && !$appInstalled) {
            $shop = $request->get('shop');
            $redirectUri = urlencode(config('app.url') . '/auth/shopify/redirect' . "?shop=$shop");
            $params = $request->query();
            unset($params['embedded']);
            $queryString = http_build_query(array_merge($params, ['redirectUri' => $redirectUri]));

            $url = config('app.url') . "/ExitIframe?$queryString";

            return redirect($url);
        }

        return $next($request);
    }
}
