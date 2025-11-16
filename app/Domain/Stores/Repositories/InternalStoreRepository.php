<?php
declare(strict_types=1);

namespace App\Domain\Stores\Repositories;

use App\Domain\Stores\Models\Store;
use App\Domain\Stores\Values\Collections\StoreCollection;
use App\Domain\Stores\Values\Store as StoreValue;
use Illuminate\Database\Eloquent\ModelNotFoundException;

class InternalStoreRepository
{
    // For internal console commands use only
    // Don't expose this method via APIs
    public function getAllUndeleted(): StoreCollection
    {
        $stores = Store::withoutCurrentStore()->withoutTrashed()->get();

        $initializedStores = [];
        foreach ($stores as $store) {
            $initializedStores[] = $this->initStoreValue($store);
        }

        /** @psalm-suppress InvalidArgument */
        return StoreValue::collection($initializedStores);
    }

    protected function initStoreValue(Store $store): StoreValue
    {
        $data = $store->toArray();
        try {
            $data['accessToken'] = $store->settings()->withSecure()->where('name', 'shopify_oauth_token')->firstOrFail()->value;
        } catch (ModelNotFoundException) {
        }

        return StoreValue::from($data);
    }
}
