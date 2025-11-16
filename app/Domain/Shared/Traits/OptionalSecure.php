<?php
declare(strict_types=1);

namespace App\Domain\Shared\Traits;

use App\Domain\Shared\Models\Casts\OptionalEncrypt;
use App\Domain\Shared\Scopes\IgnoreSecureScope;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Support\Str;

/**
 * @method static static|Builder withSecure()
 * @method static static|Builder withoutSecure()
 */
trait OptionalSecure
{
    public function getCasts(): array
    {
        return array_merge([
            'value' => OptionalEncrypt::class,
            'is_secure' => 'boolean',
            'created_at' => 'datetime',
            'updated_at' => 'datetime',
        ], $this->casts);
    }

    /**
     * @return array<array-key, string>
     */
    public function getFillable(): array
    {
        return array_merge([
            $this->getParentPrimaryKey(),
            'value',
            'is_secure',
        ], $this->fillable);
    }

    public static function bootOptionalSecure(): void
    {
        static::addGlobalScope(resolve(IgnoreSecureScope::class, ['column' => static::secureColumn()]));
    }

    protected function getParentPrimaryKey(): string
    {
        return Str::of(class_basename($this))
            ->replace(['Setting', 'Meta'], '')
            ->singular()
            ->snake()
            ->append('_', $this->getKeyName())
            ->toString();
    }

    public function scopeWithSecure(Builder $query): Builder
    {
        $query->withoutGlobalScopes([IgnoreSecureScope::class]);

        return $query;
    }

    public static function secureColumn(): string
    {
        return 'is_secure';
    }

    public function fill(array $attributes): static
    {
        if (isset($attributes[static::secureColumn()])) {
            $attributes = array_merge([static::secureColumn() => $attributes[static::secureColumn()] ?? false], $attributes);
        }

        return parent::fill($attributes);
    }
}
