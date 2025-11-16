<?php
declare(strict_types=1);

namespace App\Domain\Trials\Http\Resources;

use App\Domain\Programs\Values\Program;
use Illuminate\Http\Resources\Json\JsonResource;

/**
 * @property Program $resource
 */
class TrialResource extends JsonResource
{
    public static $wrap = 'trial';
}
