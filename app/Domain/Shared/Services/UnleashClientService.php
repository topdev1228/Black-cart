<?php
declare(strict_types=1);

namespace App\Domain\Shared\Services;

use Closure;
use GuzzleHttp\Client as GuzzleClient;
use GuzzleHttp\Handler\CurlHandler;
use GuzzleHttp\HandlerStack;
use GuzzleHttp\Promise\FulfilledPromise;
use GuzzleHttp\Promise\Promise;
use Illuminate\Contracts\Config\Repository as Config;
use Psr\Http\Message\RequestInterface;

/**
 * @psalm-suppress InvalidExtendClass
 */
class UnleashClientService extends GuzzleClient
{
    /**
     * @psalm-param Config $config
     * @psalm-suppress ConstructorSignatureMismatch
     * @psalm-suppress ImplementedParamTypeMismatch
     * @psalm-suppress MethodSignatureMismatch
     */
    public function __construct(Config $config)
    {
        $handler = fn (callable $handler): FulfilledPromise|Promise|Closure => function (RequestInterface $request, array $options) use ($handler): FulfilledPromise|Promise|Closure {
            $contents = $request->getBody()->getContents();
            if (empty($contents)) {
                $contents = '{}';
            }
            $hmac = hash_hmac('sha256', $contents, (string) config('unleash.apiKey'));
            $request = $request->withHeader(
                'Authorization',
                $hmac
            );

            return $handler($request, $options);
        };

        $stack = resolve(HandlerStack::class);
        $stack->setHandler(resolve(CurlHandler::class));
        $stack->push($handler);

        parent::__construct([
            'handler' => $stack,
            'base_uri' => $config->get('unleash.url'),
        ]);
    }
}
