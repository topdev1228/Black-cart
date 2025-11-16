<?php
declare(strict_types=1);

namespace App\Domain\Stores\Http\Resources;

use App\Domain\Stores\Values\Store;
use Illuminate\Http\Resources\Json\JsonResource;

/**
 * @property Store $resource
 */
class StoreResource extends JsonResource
{
    public static $wrap = 'store';
}
