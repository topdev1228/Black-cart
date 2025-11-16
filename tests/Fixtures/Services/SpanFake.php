<?php
declare(strict_types=1);

namespace Tests\Fixtures\Services;

use DDTrace\Contracts\Span;

class SpanFake implements Span
{
    public function getOperationName()
    {
    }

    public function getContext()
    {
    }

    public function finish($finishTime = null)
    {
    }

    public function overwriteOperationName($newOperationName)
    {
    }

    public function setResource($resource)
    {
    }

    public function setTag($key, $value, $setIfFinished = false)
    {
    }

    public function getTag($key)
    {
    }

    public function log(array $fields = [], $timestamp = null)
    {
    }

    public function addBaggageItem($key, $value)
    {
    }

    public function getBaggageItem($key)
    {
    }

    public function getAllBaggageItems()
    {
    }

    public function setError($error)
    {
    }

    public function setRawError($message, $type)
    {
    }

    public function hasError()
    {
    }

    public function getStartTime()
    {
    }

    public function getDuration()
    {
    }

    public function getTraceId()
    {
    }

    public function getSpanId()
    {
    }

    public function getParentId()
    {
    }

    public function getResource()
    {
    }

    public function getService()
    {
    }

    public function getType()
    {
    }

    public function isFinished()
    {
    }

    public function getAllTags()
    {
    }

    public function hasTag($name)
    {
    }

    public function setMetric($key, $value)
    {
    }

    public function getMetrics()
    {
    }

    public function setTraceAnalyticsCandidate($value = true)
    {
    }

    public function isTraceAnalyticsCandidate()
    {
    }
}
