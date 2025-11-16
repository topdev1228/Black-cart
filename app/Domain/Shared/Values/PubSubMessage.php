<?php

declare(strict_types=1);

namespace App\Domain\Shared\Values;

use App\Domain\Shared\Traits\HasValueFactory;
use App\Domain\Shared\Values\Casts\Collection as CollectionCast;
use Illuminate\Support\Collection;
use Spatie\LaravelData\Attributes\WithCast;

class PubSubMessage extends Value
{
    use HasValueFactory;

    public function __construct(
        public string $messageId,
        #[WithCast(CollectionCast::class)]
        public Collection $data,
        #[WithCast(CollectionCast::class)]
        public Collection $attributes,
    ) {
    }
}
