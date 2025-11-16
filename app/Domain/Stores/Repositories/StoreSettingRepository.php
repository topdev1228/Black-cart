<?php
declare(strict_types=1);

namespace App\Domain\Stores\Repositories;

use App;
use App\Domain\Stores\Enums\StoreStatus;
use App\Domain\Stores\Events\StoreCreated;
use App\Domain\Stores\Events\StoreStatusChangedEvent;
use App\Domain\Stores\Models\StoreSetting;
use App\Domain\Stores\Values\Collections\StoreSettingCollection;
use App\Domain\Stores\Values\StoreSetting as StoreSettingValue;

class StoreSettingRepository
{
    public function all(): StoreSettingCollection
    {
        /** @psalm-suppress InvalidArgument */
        return StoreSettingValue::collection(StoreSetting::all());
    }

    public function getByName(string $name): StoreSettingValue
    {
        return StoreSettingValue::from(StoreSetting::where(['name' => $name])->firstOrFail());
    }

    public function save(StoreSettingValue|StoreSettingCollection $storeSettings): StoreSettingCollection
    {
        if ($storeSettings instanceof StoreSettingValue) {
            $storeSettings = StoreSettingValue::collection([$storeSettings]);
        }

        $storeSettings->map(function (StoreSettingValue $storeSetting) {
            $values = [
                'store_id' => App::context()->store->id,
                'value' => $storeSetting->value,
            ];

            if ($storeSetting->isSecure !== null) {
                $values['is_secure'] = $storeSetting->isSecure;
            }

            /** @psalm-suppress UndefinedMagicMethod */
            $storeSetting = StoreSettingValue::from(StoreSetting::withSecure()->updateOrCreate(
                [
                    'name' => $storeSetting->name,
                ],
                $values,
            ));

            if ($storeSetting->name === 'shopify_oauth_token') {
                App::context()->store->accessToken = $storeSetting->value;
                StoreSetting::updateOrCreate(
                    [
                        'name' => 'status',
                    ],
                    [
                        'store_id' => App::context()->store->id,
                        'value' => StoreStatus::ONBOARDING->value,
                    ],
                );
                StoreCreated::dispatch(App::context()->store);
                StoreStatusChangedEvent::dispatch(App::context()->store, StoreStatus::ONBOARDING->value);
            }

            if ($storeSetting->name === 'status' && $storeSetting->value) {
                StoreStatusChangedEvent::dispatch(App::context()->store, $storeSetting->value);
            }

            return $storeSetting;
        });

        return $this->all();
    }

    public function delete(string $name): void
    {
        StoreSetting::where('name', $name)->delete();
    }

    public function deleteAll()
    {
        StoreSetting::query()->delete();
    }
}
