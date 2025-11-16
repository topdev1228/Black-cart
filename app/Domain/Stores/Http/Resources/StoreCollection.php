<?php
declare(strict_types=1);

namespace App\Domain\Stores\Http\Resources;

use Illuminate\Http\Resources\Json\ResourceCollection;

class StoreCollection extends ResourceCollection
{
    public static $wrap = 'stores';
}
