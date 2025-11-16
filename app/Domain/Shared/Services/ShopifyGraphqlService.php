<?php
declare(strict_types=1);

namespace App\Domain\Shared\Services;

use App;
use App\Domain\Shared\Exceptions\Shopify\ShopifyAuthenticationException;
use App\Domain\Shared\Exceptions\Shopify\ShopifyClientException;
use App\Domain\Shared\Exceptions\Shopify\ShopifyMutationClientException;
use App\Domain\Shared\Exceptions\Shopify\ShopifyMutationServerException;
use App\Domain\Shared\Exceptions\Shopify\ShopifyQueryClientException;
use App\Domain\Shared\Exceptions\Shopify\ShopifyQueryServerException;
use App\Domain\Shared\Exceptions\Shopify\ShopifyServerException;
use App\Domain\Shared\Facades\AppMetrics;
use Illuminate\Http\Client\Response;
use Illuminate\Support\Facades\Http;
use function implode;
use function str_starts_with;

class ShopifyGraphqlService
{
    protected string $url;
    protected array $headers;

    public function __construct(protected string $apiVersion)
    {
    }

    public function setApiVersion($apiVersion): self
    {
        $this->apiVersion = $apiVersion;

        return $this;
    }

    protected function initialize(): void
    {
        $this->url = sprintf(
            'https://%s/admin/api/%s/graphql.json',
            App::context()->store->domain,
            $this->apiVersion
        );
        $this->headers = [
            'X-Shopify-Access-Token' => App::context()->store->accessToken,
        ];
    }

    /**
     * @throws ShopifyAuthenticationException
     * @throws ShopifyClientException
     * @throws ShopifyServerException
     * @throws ShopifyQueryClientException
     * @throws ShopifyQueryServerException
     * @throws ShopifyMutationClientException
     * @throws ShopifyMutationServerException
     */
    public function post(string $query, ?array $variables = null): array
    {
        return AppMetrics::trace('shopify.graphql', function (MetricsService $metrics) use ($query, $variables) {
            $this->initialize();

            $metrics->setTag('http.client.url', $this->url);
            $metrics->setTag('http.client.has_token', $this->headers['X-Shopify-Access-Token'] !== null ? 'true' : 'false');
            $metrics->setTag('shopify.graphql.query', $query);
            $metrics->setTag('shopify.graphql.variables', $variables !== null ? $variables : []);

            $data = ['query' => $query];
            if ($variables !== null) {
                $data['variables'] = $variables;
            }

            $response = Http::withHeaders($this->headers)
                ->acceptJson()
                ->post($this->url, $data)
                ->onError(function (Response $response) {
                    $errorMessage = $response->toException()->getMessage();

                    if ($response->clientError()) {
                        if ($response->status() === 401) {
                            throw new ShopifyAuthenticationException($response->json('errors'), $response->toException());
                        }
                        throw new ShopifyClientException(message: $errorMessage, previous: $response->toException());
                    }
                    throw new ShopifyServerException(message: $errorMessage, previous: $response->toException());
                })
                ->json();

            $isMutation = str_starts_with($query, 'mutation');
            if (!empty($response['errors'])) {
                if ($isMutation) {
                    throw new ShopifyMutationServerException(
                        isset($response['errors'][0]['path']) ? implode('->', $response['errors'][0]['path']) : 'unknown',
                        $response['errors'][0]['message']
                    );
                }

                throw new ShopifyQueryServerException(
                    isset($response['errors'][0]['path']) ? implode('->', $response['errors'][0]['path']) : 'unknown',
                    $response['errors'][0]['message']
                );
            }

            $name = array_keys($response['data'])[0];
            if (!empty($response['data'][$name]['userErrors'])) {
                if ($isMutation) {
                    throw new ShopifyMutationClientException(
                        $name,
                        $response['data'][$name]['userErrors'][0]['message'],
                    );
                }

                throw new ShopifyQueryClientException(
                    $name,
                    $response['data'][$name]['userErrors'][0]['message'],
                );
            }

            return $response;
        });
    }

    /**
     * @throws ShopifyAuthenticationException
     * @throws ShopifyClientException
     * @throws ShopifyServerException
     * @throws ShopifyQueryClientException
     * @throws ShopifyQueryServerException
     * @throws ShopifyMutationClientException
     * @throws ShopifyMutationServerException
     */
    public function postMutation(string $queryString, ?array $variables = null): array
    {
        return $this->post($queryString, $variables);
    }
}
