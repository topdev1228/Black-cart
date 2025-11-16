<?php
declare(strict_types=1);

namespace App\Domain\Programs\Services;

use App\Domain\Programs\Repositories\ProgramRepository;
use App\Domain\Programs\Values\Collections\ProgramCollection;
use App\Domain\Programs\Values\Program as ProgramValue;

class ProgramService
{
    public function __construct(
        protected ProgramRepository $programRepository,
        protected ShopifyProgramService $shopifyProgramService
    ) {
    }

    public function all(): ProgramCollection
    {
        return $this->programRepository->all();
    }

    public function getById(string $id): ProgramValue
    {
        return $this->programRepository->getById($id);
    }

    public function create(ProgramValue $programValue): ProgramValue
    {
        $programWithShopifyIds = $this->shopifyProgramService->create($programValue);

        return $this->programRepository->store($programWithShopifyIds);
    }

    public function update(string $id, array $updateProgramValues): ProgramValue
    {
        $currentProgramValue = $this->getById($id);

        $newProgramValueArray = $currentProgramValue->toArray();
        foreach ($updateProgramValues as $property => $value) {
            $newProgramValueArray[$property] = $value;
        }
        $programValueWithUpdates = ProgramValue::from($newProgramValueArray);

        $shouldUpdate = false;
        foreach ($programValueWithUpdates as $property => $value) {
            if ($value !== $currentProgramValue->$property) {
                $shouldUpdate = true;
                break;
            }
        }
        if (!$shouldUpdate) {
            return $currentProgramValue;
        }

        $this->shopifyProgramService->update($programValueWithUpdates);

        return $this->programRepository->update($id, $updateProgramValues);
    }
}
