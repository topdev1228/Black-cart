<?php
declare(strict_types=1);

namespace App\Domain\Shared\Http\Client\Middleware;

use App;
use App\Domain\Shared\Facades\AppMetrics;
use App\Domain\Shopify\Values\JwtPayload;
use App\Domain\Shopify\Values\JwtToken;
use Firebase\JWT\JWT;
use Psr\Http\Message\RequestInterface;
use Route;

class JwtAuthentication
{
    public function __invoke(RequestInterface $request): RequestInterface
    {
        if ($request->hasHeader('Authorization') || !Route::domains($request->getUri()->getHost())) {
            return $request;
        }

        if (empty(App::context()->store->domain)) {
            AppMetrics::setGlobalError('HTTP Client unable to set JWT token. Store domain is empty.');
        }

        if (empty(App::context()->jwtToken->token)) {
            AppMetrics::setGlobalTag('http.client.injected_jwt', true);
            App::context(jwtToken: new JwtToken(JWT::encode(
                (new JwtPayload(domain: App::context()->store->domain))->toArray(),
                config('services.shopify.client_secret'),
                'HS256'
            )));
        } else {
            AppMetrics::setGlobalTag('http.client.injected_jwt', false);
        }

        return $request->withHeader('Authorization', 'Bearer ' . (App::context()->jwtToken->token));
    }
}
