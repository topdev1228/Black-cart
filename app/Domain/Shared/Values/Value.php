<?php
declare(strict_types=1);

namespace App\Domain\Shared\Values;

use App\Domain\Shared\Exceptions\UndefinedPropertyException;
use App\Domain\Shared\Traits\HasValueCollection;
use App\Domain\Shared\Values\Collection as ValueCollection;
use Error;
use Illuminate\Support\Collection;
use function property_exists;
use ReflectionException;
use ReflectionObject;
use Spatie\LaravelData\Data;
use Spatie\LaravelData\Support\Validation\ValidationContext;
use Str;
use Validator;

/**
 * @method static array rules(ValidationContext $context)
 */
abstract class Value extends Data
{
    use HasValueCollection;

    protected array $hidden = [];

    public function exceptProperties(): array
    {
        $hidden = $this->hidden;

        foreach ($hidden as $key => $value) {
            if (is_int($key)) {
                $hidden[$value] = true;
                unset($hidden[$key]);
            }
        }

        return $hidden;
    }

    public function has(string ...$keys): bool
    {
        $values = $this->getAttributes()->toArray();

        $rules = collect($keys)->mapWithKeys(function ($key) {
            return [Str::of($key)->snake()->toString() => ['required']];
        })->toArray();

        $validator = Validator::make($values, $rules);
        $validator->validate();

        return true;
    }

    public function getAttributes(): Collection
    {
        $values = collect($this->__sleep())->filter(fn ($_, $key) => $key !== '_dataContext')->mapWithKeys(function ($key) {
            if (isset($this->{$key})) {
                return [$key => $this->{$key}];
            }

            if ($key === '_additional') {
                return $this->getAdditionalData();
            }

            return [];
        });

        return $values;
    }

    public function getOriginal(string $key = null, mixed $default = null): mixed
    {
        if ($key !== null) {
            try {
                $property = (new ReflectionObject($this))->getProperty($key);

                return $property->getValue($this);
            } catch (ReflectionException) {
                return $default;
            }
        }

        $raw = [];
        foreach ((new ReflectionObject($this))->getProperties() as $property) {
            if ($property->class === static::class) {
                if ($this->{$property->name} instanceof self) {
                    $raw[$property->name] = $this->{$property->name}->getOriginal();
                    continue;
                }

                if ($this->{$property->name} instanceof ValueCollection) {
                    $raw[$property->name] = $this->{$property->name}->toCollection()->map(fn (Value $value) => $value->getOriginal());
                    continue;
                }

                $raw[$property->name] = $this->{$property->name};
            }
        }

        return $raw;
    }

    public function toArray(): array
    {
        $values = parent::toArray();

        foreach ($values as $key => &$value) {
            $value = match (true) {
                method_exists($this, $key) => $this->{$key}(),
                method_exists($this, Str::camel($key)) => $this->{Str::camel($key)}(),
                default => $value,
            };
        }

        return $values;
    }

    public function __get(string $name): mixed
    {
        if (method_exists($this, Str::camel($name))) {
            return $this->{Str::camel($name)}();
        }

        return $this->{$name};
    }

    public function __set(string $name, mixed $value): void
    {
        $this->{$name} = $value;
    }

    public function __isset(string $name): bool
    {
        return isset($this->{$name});
    }

    public function __call(string $method, mixed $value): static
    {
        if (Str::startsWith($method, 'with')) {
            $property = Str::of($method)->after('with')->snake()->toString();

            if (!property_exists($this, $property)) {
                throw new UndefinedPropertyException($property, $this::class);
            }

            $new = clone $this;
            $new->{$property} = $value[0];

            return $new;
        }

        throw new Error(sprintf('Call to undefined method %s::%s().', static::class, $method));
    }
}
