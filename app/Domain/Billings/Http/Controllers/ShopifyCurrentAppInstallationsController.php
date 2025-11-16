<?php
declare(strict_types=1);

namespace App\Domain\Billings\Http\Controllers;

use App\Domain\Billings\Http\Resources\ShopifyCurrentAppInstallationResource;
use App\Domain\Billings\Services\ShopifyCurrentAppInstallationService;
use App\Domain\Shared\Http\Controllers\Controller;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Response;

class ShopifyCurrentAppInstallationsController extends Controller
{
    public function __construct(protected ShopifyCurrentAppInstallationService $shopifyCurrentAppInstallationServiceService)
    {
    }

    public function get(): Response|JsonResponse
    {
        return $this->sendResponse(
            new ShopifyCurrentAppInstallationResource($this->shopifyCurrentAppInstallationServiceService->get())
        );
    }
}
