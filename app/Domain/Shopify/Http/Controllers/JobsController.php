<?php
declare(strict_types=1);

namespace App\Domain\Shopify\Http\Controllers;

use App\Domain\Shared\Http\Controllers\Controller;
use App\Domain\Shopify\Http\Resources\JobResource;
use App\Domain\Shopify\Services\JobsService;
use App\Domain\Shopify\Values\Job as JobValue;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Response;

class JobsController extends Controller
{
    public function __construct(protected JobsService $jobsService)
    {
    }

    public function store(JobValue $jobValue): Response|JsonResponse
    {
        return $this->sendResponse(
            new JobResource($this->jobsService->create($jobValue)),
            Response::HTTP_CREATED
        );
    }
}
