<?php
declare(strict_types=1);

namespace App\Domain\Billings\Http\Resources;

use App\Domain\Billings\Models\SubscriptionLineItem;
use Illuminate\Http\Resources\Json\JsonResource;

/**
 * @property SubscriptionLineItem $resource
 */
class SubscriptionLineItemResource extends JsonResource
{
    public static $wrap = 'line_item';
}
