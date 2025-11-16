<?php
declare(strict_types=1);

namespace App\Domain\Programs\Services;

use App\Domain\Programs\Repositories\ProgramRepository;
use App\Domain\Programs\Values\Program as ProgramValue;

class ProgramVariantService
{
    public function __construct(
        protected ProgramRepository $programRepository,
        protected ShopifyProgramVariantService $shopifyProgramVariantService
    ) {
    }

    public function getById(string $id): ProgramValue
    {
        return $this->programRepository->getById($id);
    }

    public function addVariants(string $id, array $variants)
    {
        $program = $this->getById($id);
        $this->shopifyProgramVariantService->addProductVariants($program->shopifySellingPlanGroupId, $variants);
    }

    public function variantsInSellingPlan(string $id, array $variants)
    {
        $program = $this->getById($id);

        return $this->shopifyProgramVariantService->variantsInSellingPlan($program->shopifySellingPlanGroupId, $variants);
    }

    public function removeVariants(string $id, array $variants)
    {
        $program = $this->getById($id);
        $this->shopifyProgramVariantService->removeProductVariants($program->shopifySellingPlanGroupId, $variants);
    }
}
