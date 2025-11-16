<?php
declare(strict_types=1);

namespace App\Domain\Shopify\Http\Resources;

use App\Domain\Shopify\Values\Job;
use Illuminate\Http\Resources\Json\JsonResource;

/**
 * @property Job $resource
 */
class JobResource extends JsonResource
{
    public static $wrap = 'job';
}
