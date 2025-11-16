<?php
declare(strict_types=1);

namespace App\Domain\Shared\Values;

use Illuminate\Database\Eloquent\Collection as EloquentCollection;
use Illuminate\Foundation\Testing\WithFaker;
use Illuminate\Support\Collection;
use Illuminate\Support\Enumerable;
use ReflectionClass;
use Spatie\LaravelData\CursorPaginatedDataCollection;
use Spatie\LaravelData\Data;
use Spatie\LaravelData\DataCollection;
use Spatie\LaravelData\PaginatedDataCollection;
use Str;

abstract class Factory
{
    use WithFaker;

    protected int $count = 1;

    /**
     * @var class-string<Value>
     */
    protected string $valueClass;

    protected Collection $valueCollection;

    protected Collection $values;

    protected Collection $sequences;

    protected bool $validate = false;

    /**
     * @psalm-suppress PropertyTypeCoercion
     */
    public function __construct()
    {
        $this->setUpFaker();

        $this->valueClass = Str::of(static::class)->replace(['Factory', '\\Factories'], '')->toString();
        $this->valueCollection = Collection::empty();
        $this->values = Collection::make($this->definition());
        $this->sequences = Collection::empty();
    }

    public function state(array|callable $state): static
    {
        if ($state === []) {
            // ValueObject::factory() with no state, calls state() with an empty array
            return $this;
        }

        if (is_callable($state)) {
            $this->sequences->push($state);

            return $this;
        }

        $this->values = $this->values->merge($state);

        return $this;
    }

    /**
     * @psalm-suppress InvalidReturnType
     * @psalm-suppress InvalidReturnStatement
     */
    public function create(array $state = []): Data|DataCollection|Collection|null
    {
        for ($i = 0; $i < $this->count; $i++) {
            $this->valueCollection->push($this->makeInstance($state));
        }

        return ($this->count === 1) ? $this->valueCollection->first() : $this->newCollection($this->valueCollection);
    }

    public function make(array $state = []): Data|DataCollection|Collection|null
    {
        return $this->create($state);
    }

    public function count(?int $count = null): static
    {
        $this->count = $count ?? $this->count;

        return $this;
    }

    public function validate(bool $shouldValidate = true): static
    {
        $this->validate = $shouldValidate;

        return $this;
    }

    public function empty(): static
    {
        return $this->state($this->valueClass::empty());
    }

    /**
     * @return Collection|DataCollection|CursorPaginatedDataCollection|PaginatedDataCollection
     *
     * @psalm-return Collection|DataCollection<Value>|CursorPaginatedDataCollection<Value>|PaginatedDataCollection<Value>
     */
    protected function newCollection(array|Enumerable|DataCollection|Collection $items = []): Collection|CursorPaginatedDataCollection|DataCollection|PaginatedDataCollection|EloquentCollection
    {
        $class = new ReflectionClass($this->valueClass);
        if ($class->hasMethod('newCollection')) {
            return $class->newInstanceWithoutConstructor()->newCollection($items);
        }

        return Collection::make($items);
    }

    abstract public function definition(): array;

    protected function makeInstance(array $state = []): object
    {
        $this->values = $this->values->merge($state);
        $this->sequences->each(fn ($state) => $this->values = $this->values->merge($state($this->faker)));

        if ($this->validate) {
            return $this->valueClass::validateAndCreate($this->values->toArray());
        }

        return $this->valueClass::from($this->values->toArray());
    }
}
