<?php
declare(strict_types=1);

namespace App\Domain\Orders\Http\Controllers;

use App\Domain\Orders\Http\Resources\AnalyticsResource;
use App\Domain\Orders\Services\AnalyticsService;
use App\Domain\Shared\Http\Controllers\Controller;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Response;

class AnalyticsController extends Controller
{
    public function __construct(protected AnalyticsService $analyticsService)
    {
    }

    public function get(): Response|JsonResponse
    {
        return $this->sendResponse(
            new AnalyticsResource($this->analyticsService->get())
        );
    }
}
