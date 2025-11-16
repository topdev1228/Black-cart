<?php
declare(strict_types=1);

namespace App\Domain\Stores\Http\Controllers;

use App\Domain\Shared\Http\Controllers\Controller;
use App\Domain\Stores\Http\Resources\StoreCollection;
use App\Domain\Stores\Http\Resources\StoreResource;
use App\Domain\Stores\Services\StoreService;
use App\Domain\Stores\Values\Store as StoreValue;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Response;

class StoreController extends Controller
{
    public function __construct(protected StoreService $storeService)
    {
    }

    public function index(): Response|JsonResponse
    {
        return $this->sendResponse(new StoreCollection($this->storeService->all()));
    }

    public function create(StoreValue $store): Response|JsonResponse
    {
        return $this->sendResponse(
            new StoreResource($this->storeService->create($store)),
            Response::HTTP_CREATED
        );
    }

    public function update(StoreValue $store): Response|JsonResponse
    {
        return $this->sendResponse(new StoreResource($this->storeService->update($store)));
    }
}
