<?php
declare(strict_types=1);

namespace App\Domain\Stores\Values;

use App\Domain\Shared\Traits\HasValueCollection;
use App\Domain\Shared\Traits\HasValueFactory;
use App\Domain\Shared\Values\Value;
use App\Domain\Stores\Enums\EcommercePlatform;
use App\Domain\Stores\Values\Collections\StoreCollection;
use Carbon\CarbonImmutable;
use Illuminate\Contracts\Pagination\CursorPaginator;
use Illuminate\Contracts\Pagination\Paginator;
use Illuminate\Pagination\AbstractCursorPaginator;
use Illuminate\Pagination\AbstractPaginator;
use Illuminate\Support\Collection;
use Illuminate\Support\Enumerable;
use Illuminate\Validation\Rule;
use PrinsFrank\Standards\Country\CountryAlpha2;
use PrinsFrank\Standards\Currency\CurrencyAlpha3;
use Spatie\LaravelData\Attributes\MapName;
use Spatie\LaravelData\Attributes\WithCast;
use Spatie\LaravelData\Casts\DateTimeInterfaceCast;
use Spatie\LaravelData\DataCollection;
use Spatie\LaravelData\Mappers\SnakeCaseMapper;
use Spatie\LaravelData\Support\Validation\ValidationContext;

/**
 * @method static StoreCollection<Store> collection(Enumerable|array|AbstractPaginator|Paginator|AbstractCursorPaginator|CursorPaginator|DataCollection|Collection|null $items)
 * @psalm-suppress LessSpecificImplementedReturnType
 */
#[MapName(SnakeCaseMapper::class)]
class Store extends Value
{
    use HasValueCollection;
    use HasValueFactory;

    protected array $hidden = [
        'id',
        'accessToken',
    ];

    public function __construct(
        public ?string $id = null,
        public ?string $accessToken = null,
        public ?string $name = '',
        public ?string $email = '',
        public ?string $phone = '',
        public ?string $domain = '',
        public ?string $ownerName = '',
        public ?CurrencyAlpha3 $currency = CurrencyAlpha3::US_Dollar,
        public ?string $primaryLocale = 'en',
        public ?string $address1 = '',
        public ?string $address2 = null,
        public ?string $city = '',
        public ?string $state = '',
        public ?string $stateCode = '',
        public ?string $country = 'US',
        public ?CountryAlpha2 $countryCode = CountryAlpha2::United_States_of_America,
        public ?string $countryName = 'US',
        public ?string $ianaTimezone = 'America/New_York',
        public ?EcommercePlatform $ecommercePlatform = EcommercePlatform::SHOPIFY,
        public ?string $ecommercePlatformStoreId = '',
        public ?string $ecommercePlatformPlan = '',
        public ?string $ecommercePlatformPlanName = '',
        public ?string $source = null,
        #[WithCast(DateTimeInterfaceCast::class)]
        public ?CarbonImmutable $createdAt = null
    ) {
    }

    public static function rules(ValidationContext $context): array
    {
        return [
            'name' => ['required', 'string'],
            'domain' => ['required', 'string'],
            'email' => ['nullable', 'email'],
            'phone' => ['nullable', 'string'],
            'owner_name' => ['nullable', 'string'],
            'currency' => ['nullable', 'string', 'size:3'],
            'primary_locale' => ['nullable', 'string', 'size:2'],
            'address1' => ['nullable', 'string'],
            'address2' => ['nullable', 'string'],
            'city' => ['nullable', 'string'],
            'state' => ['nullable', 'string'],
            'state_code' => ['nullable', 'string'],
            'country' => ['nullable', 'string'],
            'country_code' => ['nullable', 'string', 'size:2'],
            'country_name' => ['nullable', 'string'],
            'iana_timezone' => ['nullable', 'string'],
            'ecommerce_platform' => ['nullable', 'string', Rule::in([EcommercePlatform::SHOPIFY])],
            'ecommerce_platform_store_id' => ['nullable', 'integer'],
            'ecommerce_platform_plan' => ['nullable', 'string'],
            'ecommerce_platform_plan_name' => ['nullable', 'string'],
            'source' => ['nullable', 'string'],
            'created_at' => ['nullable', 'date_format:Y-m-d\TH:i:sP'],
        ];
    }
}
