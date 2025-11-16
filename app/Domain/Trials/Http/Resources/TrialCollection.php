<?php
declare(strict_types=1);

namespace App\Domain\Trials\Http\Resources;

use Illuminate\Http\Resources\Json\ResourceCollection;

class TrialCollection extends ResourceCollection
{
    public static $wrap = 'trials';
}
