<?php
declare(strict_types=1);

namespace App\Domain\Stores\Repositories;

use App\Domain\Shared\Facades\AppMetrics;
use App\Domain\Shared\Services\MetricsService;
use App\Domain\Stores\Models\Store;
use App\Domain\Stores\Values\Collections\StoreCollection;
use App\Domain\Stores\Values\Store as StoreValue;
use Illuminate\Database\Eloquent\ModelNotFoundException;
use Str;

class StoreRepository
{
    public function __construct(protected StoreSettingRepository $storeSettingRepository)
    {
    }

    public function getById(string $id): StoreValue
    {
        $store = Store::findOrFail($id);

        return $this->initStoreValue($store);
    }

    public function getByIdUnsafe(string $id): StoreValue
    {
        $store = Store::withoutCurrentStore()->findOrFail($id);

        return $this->initStoreValue($store);
    }

    public function getByDomain(string $domain): StoreValue
    {
        return AppMetrics::trace('store.get', function (MetricsService $metrics) use ($domain): StoreValue {
            $domain = Str::of($domain)->replace('https://', '')->replace('http://', '')->__toString();
            $store = Store::withoutCurrentStore()->where('domain', $domain)->firstOrFail();

            if (!$metrics->hasGlobalTag('merchant.id')) {
                $metrics->setGlobalTag('merchant.id', $store->id);
                $metrics->setGlobalTag('merchant.domain', $store->domain);
            } else {
                $metrics->unsetGlobalTag('merchant.id');
                $metrics->unsetGlobalTag('merchant.domain');
            }

            $metrics->setTag('merchant.id', $store->id);
            $metrics->setTag('merchant.domain', $store->domain);

            return $this->initStoreValue($store);
        });
    }

    public function update(StoreValue $storeValue): StoreValue
    {
        $store = Store::where('domain', $storeValue->domain)->firstOrFail();
        $store->update($storeValue->toArray());

        return $this->initStoreValue($store);
    }

    public function create(StoreValue $storeValue): StoreValue
    {
        return StoreValue::from(Store::create($storeValue->toArray()));
    }

    public function all(): StoreCollection
    {
        /** @psalm-suppress InvalidArgument */
        return StoreValue::collection(Store::all());
    }

    public function delete(StoreValue $storeValue): void
    {
        $store = Store::where('domain', $storeValue->domain)->firstOrFail();
        $store->delete();
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
