<?php
declare(strict_types=1);

namespace App\Domain\Programs\Http\Controllers;

use App\Domain\Programs\Http\Requests\ProgramPutRequest;
use App\Domain\Programs\Http\Resources\ProgramCollection;
use App\Domain\Programs\Http\Resources\ProgramResource;
use App\Domain\Programs\Services\ProgramProductService;
use App\Domain\Programs\Services\ProgramService;
use App\Domain\Programs\Services\ProgramVariantService;
use App\Domain\Programs\Values\Program as ProgramValue;
use App\Domain\Shared\Http\Controllers\Controller;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Http\Response;

class ProgramController extends Controller
{
    public function __construct(protected ProgramService $programService, protected ProgramVariantService $programVariantService, protected ProgramProductService $programProductService)
    {
    }

    public function index(): Response|JsonResponse
    {
        return $this->sendResponse(new ProgramCollection($this->programService->all()));
    }

    public function store(ProgramValue $input): Response|JsonResponse
    {
        return $this->sendResponse(
            new ProgramResource($this->programService->create($input)),
            Response::HTTP_CREATED
        );
    }

    public function update(string $id, ProgramPutRequest $input): Response|JsonResponse
    {
        return $this->sendResponse(
            new ProgramResource($this->programService->update($id, $input->toArray())),
        );
    }

    public function addVariants(string $id, FormRequest $input)
    {
        $this->programVariantService->addVariants($id, $input->selected_variant_ids);

        return $this->sendResponse([], Response::HTTP_CREATED);
    }

    public function variantsInSellingPlan(string $id, Request $request)
    {
        $shopifyVariantIds = $request->input('shopify_variant_ids');
        $variantIdsArray = explode(',', $shopifyVariantIds);

        return $this->programVariantService->variantsInSellingPlan($id, $variantIdsArray);
    }

    public function removeVariants(string $id, FormRequest $input)
    {
        $this->programVariantService->removeVariants($id, $input->selected_variant_ids);

        return $this->sendResponse([], 200);
    }

    public function removeProducts(string $id, FormRequest $input)
    {
        $this->programProductService->removeProducts($id, $input->selected_product_ids);

        return $this->sendResponse([], 200);
    }
}
