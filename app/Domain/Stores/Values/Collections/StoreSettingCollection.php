<?php
declare(strict_types=1);

namespace App\Domain\Stores\Values\Collections;

use App\Domain\Shared\Values\Collection;
use App\Domain\Stores\Values\StoreSetting;
use Spatie\LaravelData\Contracts\BaseData;
use Spatie\LaravelData\DataCollection;

/**
 * @psalm-type StoreSettingCollection = DataCollection<array-key, BaseData&StoreSetting>
 */
class StoreSettingCollection extends Collection
{
}
