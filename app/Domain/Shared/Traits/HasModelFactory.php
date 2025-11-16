<?php
declare(strict_types=1);

namespace App\Domain\Shared\Traits;

use App\Domain\Shared\Values\Factory;
use function class_exists;
use Illuminate\Database\Eloquent\Factories\Factory as EloquentFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Str;
use ReflectionClass;

trait HasModelFactory
{
    /**
     * Get a new factory instance for the model.
     *
     * @psalm-suppress TooManyTemplateParams
     * @psalm-suppress MoreSpecificReturnType
     * @return EloquentFactory<static>|Factory<static>
     */
    public static function factory(callable|int|array|null $count = null, array|callable $state = [])
    {
        /** @var class-string<Model> $modelName */
        $modelName = get_called_class();
        $factory = static::newFactory() ?? EloquentFactory::factoryForModel($modelName);

        return $factory
            ->count(is_numeric($count) ? $count : null)
            ->state(is_callable($count) || is_array($count) ? $count : $state);
    }

    /**
     * @psalm-suppress MoreSpecificReturnType
     */
    protected static function newFactory(): Factory|EloquentFactory|null
    {
        /** @psalm-suppress ArgumentTypeCoercion */
        EloquentFactory::guessModelNamesUsing(function ($factory) {
            return (string) \Str::of(get_class($factory))
                ->replace('Database\\Factories', 'Models')
                ->replace('Factory', '');
        });

        $className = (string) Str::of(static::class)
            ->replace('Models', 'Database\\Factories')
            ->replace('Values', 'Values\\Factories')
            ->append('Factory');

        if (!class_exists($className)) {
            return null;
        }

        return (new ReflectionClass($className))->newInstance();
    }
}
