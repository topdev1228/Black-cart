<?php
declare(strict_types=1);

namespace App\Domain\Shared\Http\Middleware;

use Closure;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Http\Response;

class AccessControlHeaders
{
    /**
     * Ensures that Access Control Headers are set for embedded apps.
     */
    public function handle(Request $request, Closure $next): RedirectResponse|Response
    {
        /** @var Response $response */
        $response = $next($request);

        $response->headers->set('Access-Control-Allow-Origin', '*');
        $response->headers->set('Access-Control-Allow-Header', 'Authorization');
        $response->headers->set('Access-Control-Expose-Headers', 'X-Shopify-API-Request-Failure-Reauthorize-Url');

        return $response;
    }
}
