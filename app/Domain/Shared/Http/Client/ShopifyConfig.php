<?php
declare(strict_types=1);

namespace App\Domain\Shared\Http\Client;

use GuzzleHttp\Psr7\HttpFactory;
use Spawnia\Sailor\Client;
use Spawnia\Sailor\Client\Psr18;
use Spawnia\Sailor\EndpointConfig;

class ShopifyConfig extends EndpointConfig
{
    public function __construct(protected string $domain, protected string $version, protected bool $isStable = true)
    {
    }

    /**
     * Instantiate a client for Sailor to use for querying.
     *
     * You may use one of the built-in clients, such as Guzzle, or
     * bring your own implementation.
     *
     * Configuring the client is up to you. Since this configuration
     * file is just PHP code, you can do anything. For example, you
     * can use environment variables to enable a dynamic config.
     */
    public function makeClient(): Client
    {
        $client = resolve(ShopifyHttpClient::class);
        $client->setVersion(strtolower($this->version));

        return new Psr18($client, requestFactory: new HttpFactory(), streamFactory: new HttpFactory());
    }

    /**
     * The namespace the generated classes will be created in.
     */
    public function namespace(): string
    {
        return sprintf('App\\Domain\\%s\\GraphQL\\Shopify\\%s', $this->domain, $this->isStable ? 'Stable' : 'Unstable');
    }

    /**
     * Path to the directory where the generated classes will be put.
     */
    public function targetPath(): string
    {
        return sprintf(realpath(__DIR__ . '/../../../../../app/Domain') . '/%s/GraphQL/Shopify/%s', $this->domain, $this->isStable ? 'Stable' : 'Unstable');
    }

    /**
     * Where to look for .graphql files containing operations.
     */
    public function searchPath(): string
    {
        return sprintf(realpath(__DIR__ . '/../../../../../') . '/app/Domain/%s/GraphQL/Queries/Shopify/%s', $this->domain, ucfirst($this->version));
    }

    /**
     * The location of the schema file that describes the endpoint.
     */
    public function schemaPath(): string
    {
        return realpath(__DIR__ . '/../../../../../graphql/shopify-' . strtolower($this->version) . '.graphql');
    }
}
