<?php
declare(strict_types=1);

namespace Tests\Unit\Domain\Shared\Services;

use App\Domain\Shared\Services\UnleashClientService;
use Config;
use GuzzleHttp\Handler\CurlHandler;
use GuzzleHttp\Handler\MockHandler;
use GuzzleHttp\Psr7\Response;
use GuzzleHttp\Psr7\Uri;
use Tests\TestCase;

class UnleashClientTest extends TestCase
{
    public function testItCreatesHttpClient(): void
    {
        Config::set('unleash.url', 'http://test');
        $client = app(UnleashClientService::class);
        $base_uri = $client->getConfig('base_uri');
        $this->assertInstanceOf(Uri::class, $base_uri);
        $this->assertEquals('http', $base_uri->getScheme());
        $this->assertEquals('test', $base_uri->getHost());
    }

    public function testItAddsHmacHeader(): void
    {
        $mock = new MockHandler([new Response(200)]);
        $this->instance(CurlHandler::class, $mock);
        Config::set('unleash.url', 'http://test');
        Config::set('unleash.apiKey', 'test');
        $client = app(UnleashClientService::class);
        $request = $client->request('GET', 'test');
        $this->assertEquals('5f5863b9805ad4e66e954a260f9cab3f2e95718798dec0bb48a655195893d10e', $mock->getLastRequest()->getHeader('Authorization')[0]);
    }
}
