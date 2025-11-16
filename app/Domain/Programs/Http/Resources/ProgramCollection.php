<?php
declare(strict_types=1);

namespace App\Domain\Programs\Http\Resources;

use Illuminate\Http\Resources\Json\ResourceCollection;

class ProgramCollection extends ResourceCollection
{
    public static $wrap = 'programs';
}
