<?php
declare(strict_types=1);

namespace App\Domain\Orders\Http\Resources;

use App\Domain\Orders\Values\Order;
use Illuminate\Http\Resources\Json\JsonResource;

/**
 * @property Order $resource
 */
class OrderResource extends JsonResource
{
    public static $wrap = 'order';
}
