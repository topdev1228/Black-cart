<?php
declare(strict_types=1);

namespace App\Domain\Stores\Http\Resources;

use App\Domain\Stores\Values\StoreSetting;
use Illuminate\Http\Resources\Json\JsonResource;

/**
 * @property StoreSetting $resource
 */
class StoreSettingResource extends JsonResource
{
    public static $wrap = 'setting';
}
