<?php
declare(strict_types=1);

namespace App\Domain\Programs\Services;

use App\Domain\Programs\Repositories\ProgramRepository;
use App\Domain\Programs\Values\Program as ProgramValue;

class ProgramProductService
{
    public function __construct(
        protected ProgramRepository $programRepository,
        protected ShopifyProgramProductService $shopifyProgramProductService
    ) {
    }

    public function getById(string $id): ProgramValue
    {
        return $this->programRepository->getById($id);
    }

    public function removeProducts(string $id, array $products)
    {
        $program = $this->getById($id);
        $this->shopifyProgramProductService->removeProducts($program->shopifySellingPlanGroupId, $products);
    }

    public function getProducts(string $id)
    {
        $program = $this->getById($id);

        return $this->shopifyProgramProductService->getProducts($program->shopifySellingPlanGroupId);
    }
}
