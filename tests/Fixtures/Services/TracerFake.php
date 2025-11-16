<?php
declare(strict_types=1);

namespace Tests\Fixtures\Services;

use DDTrace\Contracts\SpanContext;
use DDTrace\Contracts\Tracer;

class TracerFake implements Tracer
{
    public function limited()
    {
    }

    public function getScopeManager()
    {
    }

    public function getActiveSpan()
    {
    }

    public function startActiveSpan($operationName, $options = [])
    {
    }

    public function startSpan($operationName, $options = [])
    {
    }

    public function inject(SpanContext $spanContext, $format, &$carrier)
    {
    }

    public function extract($format, $carrier)
    {
    }

    public function flush()
    {
    }

    public function setPrioritySampling($prioritySampling)
    {
    }

    public function getPrioritySampling()
    {
    }

    public function startRootSpan($operationName, $options = [])
    {
    }

    public function getRootScope()
    {
    }

    public function getSafeRootSpan()
    {
    }

    public function getTracesAsArray()
    {
    }

    public function getTracesCount()
    {
    }
}
