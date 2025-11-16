<?php
declare(strict_types=1);

namespace App\Domain\Stores\Services;

use App\Domain\Stores\Repositories\StoreRepository;
use App\Domain\Stores\Repositories\StoreSettingRepository;
use App\Domain\Stores\Values\Collections\StoreCollection;
use App\Domain\Stores\Values\Collections\StoreSettingCollection;
use App\Domain\Stores\Values\Store;
use App\Domain\Stores\Values\StoreSetting;
use InvalidArgumentException;

class StoreService
{
    public function __construct(
        protected StoreRepository $storeRepository,
        protected StoreSettingRepository $storeSettingRepository
    ) {
    }

    public function getStore(?string $id = null, ?string $domain = null): Store
    {
        if ($id === null && $domain === null) {
            throw new InvalidArgumentException('Either id or domain must be provided');
        }

        if ($id !== null) {
            return $this->storeRepository->getById($id);
        }

        return $this->storeRepository->getByDomain($domain);
    }

    public function getStoreUnsafe(?string $id = null, ?string $domain = null): Store
    {
        if ($id === null && $domain === null) {
            throw new InvalidArgumentException('Either id or domain must be provided');
        }

        if ($id !== null) {
            return $this->storeRepository->getByIdUnsafe($id);
        }

        return $this->storeRepository->getByDomain($domain);
    }

    public function create(Store $store): Store
    {
        return $this->storeRepository->create($store);
    }

    public function delete(Store $store): void
    {
        $this->storeSettingRepository->deleteAll();
        $this->storeRepository->delete($store);
    }

    public function update(Store $store): Store
    {
        return $this->storeRepository->update($store);
    }

    public function all(): StoreCollection
    {
        return $this->storeRepository->all();
    }

    public function getSettings(): StoreSettingCollection
    {
        return $this->storeSettingRepository->all();
    }

    public function getSetting(string $name): StoreSetting
    {
        return $this->storeSettingRepository->getByName($name);
    }

    public function saveSettings(StoreSettingCollection $storeSettings): StoreSettingCollection
    {
        return $this->storeSettingRepository->save($storeSettings);
    }
}
