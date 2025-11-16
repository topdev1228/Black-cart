<?php
declare(strict_types=1);

namespace App\Domain\Shared\Http\Middleware;

use App\Domain\Shared\Facades\AppMetrics;
use App\Domain\Shopify\Values\JwtPayload;
use App\Domain\Shopify\Values\JwtToken;
use App\Domain\Stores\Repositories\StoreRepository;
use Closure;
use Firebase\JWT\JWT;
use Firebase\JWT\Key;
use Illuminate\Auth\AuthenticationException;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\App;
use Symfony\Component\HttpFoundation\Response;

class JwtAuthentication
{
    public function __construct(protected StoreRepository $storeRepository)
    {
    }

    public function handle(Request $request, Closure $next): Response
    {
        $payload = $this->verifyJWT($request);
        if ($payload === false) {
            throw new AuthenticationException('Authentication Invalid.');
        }

        App::context(jwtToken: JwtToken::from(['token' => $request->bearerToken()]));

        if ($payload->domain !== null) {
            AppMetrics::setGlobalTag('merchant.domain', $payload->domain);
            App::context(store: $this->storeRepository->getByDomain($payload->domain));
        }

        return $next($request);
    }

    /**
     * @return JwtPayload|false
     */
    protected function verifyJWT(Request $request): JwtPayload|bool
    {
        $jwt = $request->bearerToken();
        if (empty($jwt)) {
            return false;
        }

        return JwtPayload::from(JWT::decode($jwt, new Key(config('services.shopify.client_secret'), 'HS256')));
    }
}
