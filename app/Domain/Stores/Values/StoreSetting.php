<?php
declare(strict_types=1);

namespace App\Domain\Stores\Values;

use App\Domain\Shared\Traits\HasValueFactory;
use App\Domain\Shared\Values\Value;
use App\Domain\Stores\Values\Collections\StoreSettingCollection;
use Illuminate\Contracts\Pagination\CursorPaginator;
use Illuminate\Contracts\Pagination\Paginator;
use Illuminate\Pagination\AbstractCursorPaginator;
use Illuminate\Pagination\AbstractPaginator;
use Illuminate\Support\Enumerable;
use Spatie\LaravelData\Attributes\MapName;
use Spatie\LaravelData\DataCollection;
use Spatie\LaravelData\Mappers\SnakeCaseMapper;
use Spatie\LaravelData\Support\Validation\ValidationContext;

/**
 * @psalm-suppress LessSpecificImplementedReturnType
 * @method static StoreSettingCollection collection(Enumerable|AbstractPaginator|Paginator|AbstractCursorPaginator|CursorPaginator|DataCollection|array|null $items)
 */
#[MapName(SnakeCaseMapper::class)]
class StoreSetting extends Value
{
    use HasValueFactory;

    protected array $hidden = [
        'isSecure',
    ];

    public function __construct(
        public string $name,
        public mixed $value,
        public ?bool $isSecure = null,
    ) {
    }

    public static function rules(ValidationContext $context): array
    {
        return [
            'name' => ['required', 'string'],
            'value' => ['required'],
            'is_secure' => ['nullable', 'boolean'],
        ];
    }
}
