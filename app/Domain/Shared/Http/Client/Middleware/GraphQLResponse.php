<?php
declare(strict_types=1);

namespace App\Domain\Shared\Http\Client\Middleware;

use App\Domain\Shared\Exceptions\Shopify\ShopifyMutationClientException;
use App\Domain\Shared\Exceptions\Shopify\ShopifyMutationServerException;
use App\Domain\Shared\Exceptions\Shopify\ShopifyQueryClientException;
use App\Domain\Shared\Exceptions\Shopify\ShopifyQueryServerException;
use Closure;
use Illuminate\Support\Str;
use Psr\Http\Message\RequestInterface;
use Psr\Http\Message\ResponseInterface;

class GraphQLResponse
{
    public function __invoke(Closure $next): callable
    {
        return function (RequestInterface $request, array $options) use ($next) {
            return $next($request, $options)->then(function (ResponseInterface $response) use ($request) {
                if (\Str::endsWith($request->getUri()->getPath(), ['graphql.json', 'graphql.json/']) && $response->getStatusCode() === 200 && isset($response->getHeader('content-type')[0]) && Str::contains($response->getHeader('content-type')[0], 'application/json')) {
                    $request->getBody()->rewind();
                    $body = json_decode($request->getBody()->getContents(), true);
                    $isMutation = \Str::startsWith($body['query'], 'mutation');

                    $response->getBody()->rewind();

                    $body = collect(json_decode($response->getBody()->getContents(), true, 512, JSON_THROW_ON_ERROR))->recursive();
                    if (isset($body['errors']) && $body['errors']->isNotEmpty()) {
                        if ($isMutation) {
                            throw new ShopifyMutationServerException(
                                isset($body['errors'][0]['path']) ? implode('->', $body['errors'][0]['path']->toArray()) : 'unknown',
                                $body['errors'][0]['message']
                            );
                        }

                        throw new ShopifyQueryServerException(
                            isset($body['errors'][0]['path']) ? implode('->', $body['errors'][0]['path']->toArray()) : 'unknown',
                            $body['errors'][0]['message']
                        );
                    }

                    if ($body->pluck('*.userErrors.*.message')->flatten()->isNotEmpty() && $body->pluck('*.userErrors.*.message')->flatten()->first() !== null) {
                        if ($isMutation) {
                            throw new ShopifyMutationClientException(
                                $body['data']->keys()->filter(fn ($key) => $key !== '__typename')->first(),
                                $body->pluck('*.userErrors.*.message')->flatten()->first()
                            );
                        }

                        if ($isMutation) {
                            throw new ShopifyQueryClientException(
                                $body['data']->keys()->filter(fn ($key) => $key !== '__typename')->first(),
                                $body->pluck('*.userErrors.*.message')->flatten()->first()
                            );
                        }
                    }

                    $response->getBody()->rewind();
                }

                return $response;
            });
        };
    }
}
