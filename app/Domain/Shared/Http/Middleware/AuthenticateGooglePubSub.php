<?php
declare(strict_types=1);

namespace App\Domain\Shared\Http\Middleware;

use App;
use App\Domain\Shared\Facades\AppMetrics;
use App\Domain\Shared\Services\MetricsService;
use App\Domain\Shopify\Values\JwtPayload;
use App\Domain\Shopify\Values\JwtToken;
use App\Domain\Stores\Repositories\StoreRepository;
use Closure;
use Firebase\JWT\JWT;
use Google\Auth\AccessToken;
use Illuminate\Http\Request;

class AuthenticateGooglePubSub
{
    public function __construct(protected StoreRepository $storeRepository)
    {
    }

    public function handle(Request $request, Closure $next)
    {
        if (!app()->environment('staging', 'production')) {
            return $next($request);
        }

        if (!$request->hasHeader('Authorization')) {
            abort(403);
        }

        AppMetrics::trace('pubsub.auth', function () use ($request) {
            $jwt = explode(' ', $request->header('Authorization'))[1];
            $accessToken = new AccessToken();
            $payload = $accessToken->verify($jwt);
            if (!$payload) {
                abort(403);
            }
        });

        return AppMetrics::trace('pubsub.domain', function (MetricsService $metrics) use ($next, $request) {
            $attributes = $request->json()->all('message')['attributes'];
            $domain = $attributes['domain'] ?? $attributes['X-Shopify-Shop-Domain'] ?? null;
            if ($domain === null) {
                $metrics->setError('No domain found.');
                abort(403);
            }
            $metrics->setGlobalTag('merchant.domain', $domain);

            App::context(
                store: $this->storeRepository->getByDomain($domain),
                jwtToken: new JwtToken(JWT::encode(
                    (new JwtPayload(domain: $domain))->toArray(),
                    config('services.shopify.client_secret'),
                    'HS256'
                ))
            );

            return $next($request);
        });
    }
}
