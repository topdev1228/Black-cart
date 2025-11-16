<?php
declare(strict_types=1);

namespace App\Domain\Shared\Services;

use DDTrace\Contracts\Span;
use DDTrace\Contracts\Tracer;
use DDTrace\StartSpanOptions;
use function DDTrace\trace_function;
use function DDTrace\trace_method;
use Throwable;

/**
 * @psalm-suppress NoInterfaceProperties
 */
class MetricsService
{
    public const ERROR_MSG = 'error.msg';
    public const ERROR_TYPE = 'error.type';
    public const ERROR_STACK = 'error.stack';

    protected ?Span $span = null;
    protected bool $hasDdTrace = false;

    public function __construct(protected ?Tracer $tracer)
    {
        $this->hasDdTrace = extension_loaded('ddtrace');
    }

    public function setGlobalError(Throwable|string|bool|null $error, ?string $type = null): void
    {
        $this->setSpanError($this->tracer?->getSafeRootSpan(), $error, $type);
    }

    public function setGlobalTag(string $name, mixed $value): void
    {
        $this->setSpanTag($this->tracer?->getSafeRootSpan(), $name, $value);
    }

    public function getGlobalTag(string $name): mixed
    {
        return $this->tracer?->getSafeRootSpan()?->tags[$name] ?? null;
    }

    public function hasGlobalTag(string $name): bool
    {
        if (($span = $this->tracer?->getSafeRootSpan()) !== null) {
            /* @var \DDTrace\Data\Span $span */
            return isset($span->tags[$name]);
        }

        return false;
    }

    public function unsetGlobalTag(string $name): void
    {
        if (($span = $this->tracer?->getSafeRootSpan()) !== null) {
            /* @var \DDTrace\Data\Span $span */
            unset($span->tags[$name]);
        }
    }

    public function setGlobalMetric(string $name, int|float|bool|array $value): void
    {
        $this->setSpanMetric($this->tracer?->getSafeRootSpan(), $name, $value);
    }

    public function setError(Throwable|string|bool|null $error, ?string $type = null): void
    {
        $this->setSpanError($this->getActiveSpan(), $error, $type);
    }

    /**
     * @param array<string,int|float|string|array>|string $value
     */
    public function setTag(string $name, array|string $value): void
    {
        $this->setSpanTag($this->getActiveSpan(), $name, $value);
    }

    public function setMetric(string $name, int|float|bool|array $value): void
    {
        $this->setSpanMetric($this->getActiveSpan(), $name, $value);
    }

    public function startSpan(string $name, StartSpanOptions|array $options = []): static
    {
        $this->tracer?->startActiveSpan($name, $options);

        $span = $this->tracer?->getActiveSpan();
        $metrics = clone $this;
        $metrics->span = $span;

        return $metrics;
    }

    public function endSpan(): void
    {
        $span = $this->span;
        if ($span === null) {
            $span = $this->tracer?->getActiveSpan();
        }

        if ($span !== null) {
            $span->isFinished() || $span->finish();
        }
    }

    public function trace(string $name, callable $wrap, ?callable $callback = null, StartSpanOptions|array $options = []): mixed
    {
        $return = null;
        $e = null;
        $metrics = $this->startSpan($name, $options);
        try {
            $return = $wrap($metrics);
        } catch (Throwable $e) {
            $metrics->setError($e);
            throw $e;
        } finally {
            if ($callback !== null) {
                $callback($metrics, !isset($e) ? $return : null, $e);
            }
            $metrics->endSpan();
        }

        return $return;
    }

    /**
     * @codeCoverageIgnore
     * @psalm-suppress UndefinedFunction
     */
    public function traceMethod(string $class, string $method, callable $callback): void
    {
        if ($this->hasDdTrace) {
            trace_method($class, $method, $callback);
        }
    }

    /**
     * @codeCoverageIgnore
     * @psalm-suppress UndefinedFunction
     */
    public function traceFunction(string $function, callable $callback): void
    {
        if ($this->hasDdTrace) {
            trace_function($function, $callback);
        }
    }

    public function getActiveSpan(): ?Span
    {
        return $this->span ?? $this->tracer?->getActiveSpan();
    }

    protected function setSpanError(?Span $span, Throwable|bool|string|null $error, ?string $type): void
    {
        if ($error instanceof Throwable) {
            $span?->setError($error);
            if ($type !== null && $span !== null) {
                $span->tags[self::ERROR_TYPE] = $type;
            }

            return;
        }

        if ($type !== null) {
            $span?->setRawError((string) $error, $type);
            $this->addTrace($span);

            return;
        }

        if ($span !== null) {
            $span->hasError = is_bool($error) ? $error : true;
            if (!is_bool($error)) {
                $span->tags[self::ERROR_MSG] = $error;
            }
            $this->addTrace($span);
        }
    }

    protected function setSpanTag(?Span $span, string $name, mixed $value): void
    {
        if (!is_array($value)) {
            $span?->setTag($name, $value);

            return;
        }

        foreach ($value as $k => $v) {
            $this->setSpanTag($span, $name . '.' . $k, $v);
        }
    }

    protected function setSpanMetric(?Span $span, string $name, float|int|bool|array $value): void
    {
        if ($value === false) {
            return;
        }

        if (!is_array($value)) {
            if ($value === true || ($value >= 0 && $value <= 1)) {
                $value = (float) $value;
            }

            $span?->setMetric($name, $value);

            return;
        }

        foreach ($value as $k => $v) {
            $name .= '.' . $k;
            $this->setSpanMetric($span, $name, $v);
        }
    }

    protected function addTrace(?Span $span): void
    {
        if ($span !== null) {
            ob_start();
            debug_print_backtrace(DEBUG_BACKTRACE_IGNORE_ARGS);
            $backtrace = ob_get_clean();
            $span->tags[self::ERROR_STACK] = $backtrace;
        }
    }
}
