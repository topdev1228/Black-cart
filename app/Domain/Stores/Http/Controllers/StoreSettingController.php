<?php
declare(strict_types=1);

namespace App\Domain\Stores\Http\Controllers;

use App\Domain\Shared\Http\Controllers\Controller;
use App\Domain\Shared\Values\AppContext;
use App\Domain\Stores\Http\Resources\StoreSettingCollection;
use App\Domain\Stores\Services\StoreService;
use App\Domain\Stores\Values\StoreSettings;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Response;

class StoreSettingController extends Controller
{
    public function __construct(protected StoreService $storeService, protected AppContext $appContext)
    {
    }

    public function index(): Response|JsonResponse
    {
        return $this->sendResponse(new StoreSettingCollection($this->storeService->getSettings()));
    }

    public function save(StoreSettings $storeSettings): Response|JsonResponse
    {
        return $this->sendResponse(
            new StoreSettingCollection(
                $this->storeService->saveSettings($storeSettings->settings)
            )
        );
    }
}
