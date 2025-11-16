<?php

declare(strict_types=1);

namespace App\Domain\Shared\Facades;

use App\Domain\Shared\Services\MetricsService;
use DDTrace\StartSpanOptions;
use Illuminate\Support\Facades\Facade;
use Throwable;

/**
 * @method static void setGlobalError(Throwable|string|bool|null $error, ?string $type = null)
 * @method static void setGlobalTag(string $name, mixed $value)
 * @method static mixed getGlobalTag(string $name)
 * @method static bool hasGlobalTag(string $name)
 * @method static void unsetGlobalTag(string $name)
 * @method static void setGlobalMetric(string $name, int|float|bool|array $value)
 * @method static void setError(Throwable|string|bool|null $error, ?string $type = null)
 * @method static void setTag(string $name, mixed $value)
 * @method static void setMetric(string $name, int|float|bool|array $value)
 * @method static MetricsService startSpan(string $name, StartSpanOptions|array $options = [])
 * @method static void endSpan()
 * @method static mixed trace(string $name, callable $wrap, ?callable $callback = null, StartSpanOptions|array $options = [])
 * @method static void traceMethod(string $class, string $method, callable $callback)
 * @method static void traceFunction(string $function, callable $callback)
 * @see https://docs.datadoghq.com/tracing/trace_collection/custom_instrumentation/php/?tab=errors#parameters-of-the-tracing-closure
 */
class AppMetrics extends Facade
{
    protected static function getFacadeAccessor()
    {
        return MetricsService::class;
    }
}
