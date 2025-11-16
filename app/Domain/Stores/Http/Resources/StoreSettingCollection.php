<?php
declare(strict_types=1);

namespace App\Domain\Stores\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\ResourceCollection;

/**
 * @property StoreSettingResource[] $resource
 */
class StoreSettingCollection extends ResourceCollection
{
    public static $wrap = 'settings';

    public function toArray(Request $request): array
    {
        return $this->collection->mapWithKeys(function ($value) {
            return [$value->name => $value];
        })->toArray();
    }
}
