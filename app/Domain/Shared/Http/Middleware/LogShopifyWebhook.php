<?php
declare(strict_types=1);

namespace App\Domain\Shared\Http\Middleware;

use App\Domain\Shared\Facades\AppMetrics;
use App\Domain\Shared\Repositories\ShopifyWebhookDataRepository;
use App\Domain\Shared\Services\MetricsService;
use App\Domain\Shopify\Enums\WebhookTopic;
use Closure;
use Illuminate\Contracts\Support\Responsable;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Http\Response;
use Str;
use Throwable;

class LogShopifyWebhook
{
    public function __construct(protected ShopifyWebhookDataRepository $shopifyWebhookDataRepository)
    {
    }

    public function handle(Request $request, Closure $next): Response|JsonResponse|Responsable
    {
        if ($request->has('message.attributes.X-Shopify-Shop-Domain')) { // Google Pub/Sub
            $topic = Str::of($request->json('message.subscription'))
                ->after('shopify-webhook-')
                ->lower()
                ->toString();

            $this->trace(
                $topic,
                $request->json('message.data'),
                $request->json('message.attributes')
            );
        } elseif ($request->headers->has('x-shopify-shop-domain')) { // HTTP
            $topic = Str::of($request->header('x-shopify-topic'))
                ->replace('/', '-')
                ->toString();

            $this->trace(
                $topic,
                $request->all(),
                $request->headers->all(),
            );
        }

        return $next($request);
    }

    protected function trace(string $topic, array $data, array $attributes): void
    {
        AppMetrics::trace(
            sprintf('webhook.%s', $topic),
            function (MetricsService $metrics) use ($topic, $data, $attributes) {
                try {
                    $this->shopifyWebhookDataRepository->save(
                        WebhookTopic::from(Str::of($topic)->replace('-', '_')->upper()->toString()),
                        $data,
                        $attributes,
                    );
                } catch (Throwable $e) {
                    $metrics->setError($e);
                }
            }
        );
    }
}
