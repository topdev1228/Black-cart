<?php
declare(strict_types=1);

namespace App\Domain\Shared\Values;

use Illuminate\Support\Collection as BaseCollection;
use Spatie\LaravelData\DataCollection;

class Collection extends DataCollection
{
    /**
     * @param class-string $class
     */
    public function mapInto(string $class): BaseCollection
    {
        $items = [];
        foreach ($this->items as $value) {
            $items[] = new $class($value);
        }

        return collect($items);
    }
}
