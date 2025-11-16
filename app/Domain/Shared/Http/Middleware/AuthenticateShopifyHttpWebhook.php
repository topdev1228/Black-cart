<?php
declare(strict_types=1);

namespace App\Domain\Shared\Http\Middleware;

use App;
use App\Domain\Shopify\Values\JwtPayload;
use App\Domain\Shopify\Values\JwtToken;
use App\Domain\Stores\Repositories\StoreRepository;
use App\Exceptions\AuthenticationException;
use Closure;
use Firebase\JWT\JWT;
use Illuminate\Http\Request;

class AuthenticateShopifyHttpWebhook
{
    protected string $shopifyClientSecret;

    public function __construct(protected StoreRepository $storeRepository)
    {
        $this->shopifyClientSecret = config('services.shopify.client_secret');
    }

    public function handle(Request $request, Closure $next)
    {
        $signature = $request->header('x-shopify-hmac-sha256');
        if (!$signature) {
            throw new AuthenticationException('Missing Shopify signature header.');
        }

        $calculatedHmac = base64_encode(
            hash_hmac('sha256', (string) $request->getContent(), (string) $this->shopifyClientSecret, true)
        );
        if (!hash_equals($calculatedHmac, $signature)) {
            throw new AuthenticationException('Invalid Shopify signature header.');
        }

        $domain = $request->header('x-shopify-shop-domain');
        App::context(
            store: $this->storeRepository->getByDomain($domain),
            jwtToken: new JwtToken(JWT::encode(
                (new JwtPayload(domain: $domain))->toArray(),
                $this->shopifyClientSecret,
                'HS256'
            ))
        );

        return $next($request);
    }
}
