<?php
declare(strict_types=1);

namespace App\Domain\Shared\Http\Controllers;

use App\Domain\Shared\Facades\AppMetrics;
use App\Domain\Shared\Services\PubSubDispatchService;
use App\Domain\Shared\Values\PubSubMessageEnvelope;
use Illuminate\Http\Response;

abstract class PubSubController extends Controller
{
    public function __construct(protected PubSubDispatchService $pubSubDispatchService)
    {
    }

    public function post(PubSubMessageEnvelope $pubSubMessageEnvelope)
    {
        AppMetrics::trace('pubsub', function () use ($pubSubMessageEnvelope) {
            $this->pubSubDispatchService->dispatch($pubSubMessageEnvelope);
        });

        return response('', Response::HTTP_OK);
    }
}
