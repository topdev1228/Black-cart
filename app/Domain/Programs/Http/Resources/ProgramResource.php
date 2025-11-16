<?php
declare(strict_types=1);

namespace App\Domain\Programs\Http\Resources;

use App\Domain\Programs\Values\Program;
use Illuminate\Http\Resources\Json\JsonResource;

/**
 * @property Program $resource
 */
class ProgramResource extends JsonResource
{
    public static $wrap = 'program';
}
