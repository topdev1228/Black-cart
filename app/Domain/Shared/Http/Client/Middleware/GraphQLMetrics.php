<?php
declare(strict_types=1);

namespace App\Domain\Shared\Http\Client\Middleware;

use App\Domain\Shared\Facades\AppMetrics;
use Psr\Http\Message\RequestInterface;
use Str;

class GraphQLMetrics
{
    public function __invoke(RequestInterface $request): RequestInterface
    {
        if (Str::endsWith($request->getUri()->getPath(), ['graphql.json', 'graphql.json/'])) {
            $body = json_decode($request->getBody()->getContents(), true);
            $query = Str::of($body['query']);
            $type = $query->words(1, '')->toString();
            $name = $query->betweenFirst($type, '(')->betweenFirst($type, '{')->trim()->toString();

            $spanName = Str::contains($request->getUri()->getHost(), '.myshopify.com') ? 'shopify.graphql' : 'other.graphql';
            $metrics = AppMetrics::startSpan($spanName);
            $metrics->setTag('graphql.type', $type);
            $metrics->setTag('graphql.name', $name === '' ? 'anonymous' : $name);
            $metrics->setTag('graphql.query', $body['query']);
            $metrics->setTag('graphql.sanitized_variables', $this->sanitizeVariables($body['variables'] ?? []) ?? 'none');
            $metrics->endSpan();

            $request->getBody()->rewind();
        }

        return $request;
    }

    protected function sanitizeVariables(array $variables): ?array
    {
        $sanitized = [];
        foreach ($variables as $key => $value) {
            if (is_string($value) && Str::startsWith($value, 'gid://shopify')) {
                $sanitized[$key] = $value;
                continue;
            }

            if (is_numeric($value)) {
                $sanitized[$key] = $value;
                continue;
            }

            // Add strings that are not emails, and are either one word, or three+ words
            // This should avoid leaking sensitive information,
            if (is_string($value)) {
                $spaces = Str::of($value)->trim()->substrCount(' ');
                if (!Str::contains($value, '@') && ($spaces >= 2 || $spaces === 0)) {
                    $sanitized[$key] = $value;
                    continue;
                }
            }

            if (is_array($value)) {
                $sanitized[$key] = $this->sanitizeVariables($value);
                continue;
            }
        }

        return $sanitized === [] ? null : $sanitized;
    }
}
