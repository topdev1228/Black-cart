<?php
declare(strict_types=1);

namespace App\Domain\Shared\Http\Middleware;

use App\Domain\Shared\Facades\AppMetrics;
use function base64_decode;
use Closure;
use Illuminate\Contracts\Support\Responsable;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Http\Response;
use function json_decode;

class HandleGooglePubSubMessage
{
    public function handle(Request $request, Closure $next): Response|JsonResponse|Responsable
    {
        return AppMetrics::trace('pubsub.handle', function () use ($request, $next) {
            $json = $request->json()->all();
            $json['message']['data'] = json_decode(base64_decode($json['message']['data']), true);
            $request->replace($json);

            return $next($request);
        });
    }
}
