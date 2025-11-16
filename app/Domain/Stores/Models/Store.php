<?php
declare(strict_types=1);

namespace App\Domain\Stores\Models;

use App\Domain\Shared\Traits\CurrentStore;
use App\Domain\Shared\Traits\HasModelFactory;
use App\Domain\Stores\Enums\EcommercePlatform;
use App\Domain\Stores\Events\StoreDeletedEvent;
use Carbon\CarbonImmutable;
use Eloquent;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Database\Eloquent\Concerns\HasTimestamps;
use Illuminate\Database\Eloquent\Concerns\HasUuids;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\SoftDeletes;
use PrinsFrank\Standards\Country\CountryAlpha2;
use PrinsFrank\Standards\Currency\CurrencyAlpha3;

/**
 * App\Domain\Stores\Models\Store
 *
 * @property string $id
 * @property string $name
 * @property string $domain
 * @property string|null $email
 * @property string|null $phone
 * @property string|null $owner_name
 * @property CurrencyAlpha3|null $currency ISO 4127
 * @property string|null $primary_locale ISO 639 two-letter language code
 * @property string|null $address1
 * @property string|null $address2
 * @property string|null $city
 * @property string|null $state
 * @property string|null $state_code
 * @property string|null $country
 * @property CountryAlpha2|null $country_code ISO 3166-1 alpha-2
 * @property string|null $country_name
 * @property string|null $iana_timezone
 * @property EcommercePlatform $ecommerce_platform
 * @property string $ecommerce_platform_store_id
 * @property string|null $ecommerce_platform_plan
 * @property string|null $ecommerce_platform_plan_name
 * @property string|null $source
 * @property CarbonImmutable|null $created_at
 * @property CarbonImmutable|null $updated_at
 * @property CarbonImmutable|null $deleted_at
 * @property-read Collection<int, \App\Domain\Stores\Models\StoreSetting> $settings
 * @property-read int|null $settings_count
 * @method static Builder|Store newModelQuery()
 * @method static Builder|Store newQuery()
 * @method static Builder|Store onlyTrashed()
 * @method static Builder|Store query()
 * @method static Builder|Store whereAddress1($value)
 * @method static Builder|Store whereAddress2($value)
 * @method static Builder|Store whereCity($value)
 * @method static Builder|Store whereCountry($value)
 * @method static Builder|Store whereCountryCode($value)
 * @method static Builder|Store whereCountryName($value)
 * @method static Builder|Store whereCreatedAt($value)
 * @method static Builder|Store whereCurrency($value)
 * @method static Builder|Store whereDeletedAt($value)
 * @method static Builder|Store whereDomain($value)
 * @method static Builder|Store whereEcommercePlatform($value)
 * @method static Builder|Store whereEcommercePlatformPlan($value)
 * @method static Builder|Store whereEcommercePlatformPlanName($value)
 * @method static Builder|Store whereEcommercePlatformStoreId($value)
 * @method static Builder|Store whereEmail($value)
 * @method static Builder|Store whereIanaTimezone($value)
 * @method static Builder|Store whereId($value)
 * @method static Builder|Store whereName($value)
 * @method static Builder|Store whereOwnerName($value)
 * @method static Builder|Store wherePhone($value)
 * @method static Builder|Store wherePrimaryLocale($value)
 * @method static Builder|Store whereSource($value)
 * @method static Builder|Store whereState($value)
 * @method static Builder|Store whereStateCode($value)
 * @method static Builder|Store whereUpdatedAt($value)
 * @method static Builder|Store withTrashed()
 * @method static Builder|Store withoutCurrentStore()
 * @method static Builder|Store withoutTrashed()
 * @mixin Eloquent
 */
class Store extends Model
{
    use HasUuids;
    use HasModelFactory;
    use SoftDeletes;
    use CurrentStore;
    use HasTimestamps;

    protected $fillable = [
        'name',
        'domain',
        'email',
        'phone',
        'owner_name',
        'currency',
        'primary_locale',
        'address1',
        'address2',
        'city',
        'state',
        'state_code',
        'country',
        'country_code',
        'country_name',
        'iana_timezone',
        'ecommerce_platform',
        'ecommerce_platform_store_id',
        'ecommerce_platform_plan',
        'ecommerce_platform_plan_name',
        'source',
    ];

    protected $dispatchesEvents = [
        'deleted' => StoreDeletedEvent::class,
    ];

    protected function casts(): array
    {
        return [
            'currency' => CurrencyAlpha3::class,
            'country_code' => CountryAlpha2::class,
            'ecommerce_platform' => EcommercePlatform::class,
            'created_at' => 'datetime:Y-m-d\TH:i:sP',
            'updated_at' => 'datetime:Y-m-d\TH:i:sP',
            'deleted_at' => 'datetime:Y-m-d\TH:i:sP',
        ];
    }

    public function getStoreIdColumn(): string
    {
        return $this->primaryKey;
    }

    public function settings(): HasMany
    {
        /** @psalm-var HasMany $relationship */
        $relationship = $this->hasMany(StoreSetting::class)->withoutCurrentStore();

        return $relationship;
    }
}
