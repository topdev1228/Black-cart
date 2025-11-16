<?php
declare(strict_types=1);

namespace App\Domain\Billings\Http\Resources;

use App\Domain\Billings\Values\Subscription;
use Illuminate\Http\Resources\Json\JsonResource;

/**
 * @property Subscription $resource
 */
class SubscriptionResource extends JsonResource
{
    public static $wrap = 'subscription';
}
