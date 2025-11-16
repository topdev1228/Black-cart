<?php
declare(strict_types=1);

namespace App\Domain\Stores\Values;

use App\Domain\Shared\Traits\HasValueFactory;
use App\Domain\Shared\Values\Value;
use App\Domain\Stores\Values\Casts\KeyValuePair;
use App\Domain\Stores\Values\Collections\StoreSettingCollection;
use Spatie\LaravelData\Attributes\DataCollectionOf;
use Spatie\LaravelData\Attributes\WithCast;
use Spatie\LaravelData\Attributes\WithTransformer;
use Spatie\LaravelData\Support\Validation\ValidationContext;

class StoreSettings extends Value
{
    use HasValueFactory;

    public function __construct(
        #[DataCollectionOf(StoreSetting::class)]
        #[WithTransformer(KeyValuePair::class)]
        #[WithCast(KeyValuePair::class)]
        public StoreSettingCollection $settings,
    ) {
    }

    public static function rules(ValidationContext $context): array
    {
        return [
            'settings' => ['required', 'array'],
            'settings.*' => ['required', 'array'],
            'settings.*.name' => ['required', 'string'],
            'settings.*.value' => ['required'],
            'settings.*.is_secure' => ['nullable', 'boolean'],
        ];
    }
}
