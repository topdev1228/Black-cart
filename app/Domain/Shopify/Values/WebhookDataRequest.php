<?php
declare(strict_types=1);

namespace App\Domain\Shopify\Values;

use App\Domain\Shared\Traits\HasValueFactory;
use App\Domain\Shared\Values\Value;
use Spatie\LaravelData\Attributes\MapName;
use Spatie\LaravelData\Mappers\SnakeCaseMapper;
use Spatie\LaravelData\Support\Validation\ValidationContext;

#[MapName(SnakeCaseMapper::class)]
class WebhookDataRequest extends Value
{
    use HasValueFactory;

    /*
        {
            "id": 191167
        }
    */

    public function __construct(
        public int $id,
    ) {
    }

    public static function rules(ValidationContext $context): array
    {
        return [
            'id' => ['required', 'int'],
        ];
    }
}
