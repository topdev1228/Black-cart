<?php
declare(strict_types=1);

namespace Tests\Unit\Domain\Shared\Services {
    use App\Domain\Shared\Services\MetricsService;
    use Exception;
    use Mockery\MockInterface;
    use Tests\Fixtures\Services\SpanFake;
    use Tests\Fixtures\Services\TracerFake;
    use Tests\TestCase;

    class MetricsServiceTest extends TestCase
    {
        public function testItSetsGlobalErrorException(): void
        {
            $e = new Exception();
            $span = $this->mock(SpanFake::class, function (MockInterface $mock) use ($e) {
                $mock->shouldReceive('setError')->with($e)->once();
            });
            $tracer = $this->mock(TracerFake::class, function (MockInterface $mock) use ($span) {
                $mock->shouldReceive('getSafeRootSpan')->andReturn($span)->once();
            });
            $metrics = new MetricsService($tracer);
            $metrics->setGlobalError($e);
        }

        public function testItSetsGlobalErrorExceptionType(): void
        {
            $e = new Exception();
            $span = $this->mock(SpanFake::class, function (MockInterface $mock) use ($e) {
                $mock->shouldReceive('setError')->with($e)->once();
            });
            $tracer = $this->mock(TracerFake::class, function (MockInterface $mock) use ($span) {
                $mock->shouldReceive('getSafeRootSpan')->andReturn($span)->once();
            });
            $metrics = new MetricsService($tracer);
            $metrics->setGlobalError($e, 'test');
            $this->assertEquals('test', $span->tags[MetricsService::ERROR_TYPE]);
        }

        public function testItSetsGlobalErrorStringType(): void
        {
            $span = $this->mock(SpanFake::class, function (MockInterface $mock) {
                $mock->shouldReceive('setRawError')->with('error', 'test')->once();
            });
            $tracer = $this->mock(TracerFake::class, function (MockInterface $mock) use ($span) {
                $mock->shouldReceive('getSafeRootSpan')->andReturn($span)->once();
            });
            $metrics = new MetricsService($tracer);
            $metrics->setGlobalError('error', 'test');
        }

        public function testItSetsGlobalErrorString(): void
        {
            $span = $this->mock(SpanFake::class, function (MockInterface $mock) {
                $mock->shouldReceive('setRawError')->never();
            });
            $tracer = $this->mock(TracerFake::class, function (MockInterface $mock) use ($span) {
                $mock->shouldReceive('getSafeRootSpan')->andReturn($span)->once();
            });
            $metrics = new MetricsService($tracer);
            $metrics->setGlobalError('error');
            $this->assertTrue($span->hasError);
            $this->assertEquals($span->tags[MetricsService::ERROR_MSG], 'error');
            $this->assertIsString($span->tags[MetricsService::ERROR_STACK]);
            $this->assertStringStartsWith('#0', $span->tags[MetricsService::ERROR_STACK]);
        }

        public function testItSetsGlobalErrorBoolean(): void
        {
            $span = $this->mock(SpanFake::class, function (MockInterface $mock) {
                $mock->shouldReceive('setRawError')->never();
            });
            $tracer = $this->mock(TracerFake::class, function (MockInterface $mock) use ($span) {
                $mock->shouldReceive('getSafeRootSpan')->andReturn($span)->once();
            });
            $metrics = new MetricsService($tracer);
            $metrics->setGlobalError(true);
            $this->assertTrue($span->hasError);
            $this->assertFalse(isset($span->tags[MetricsService::ERROR_MSG]));
            $this->assertIsString($span->tags[MetricsService::ERROR_STACK]);
            $this->assertStringStartsWith('#0', $span->tags[MetricsService::ERROR_STACK]);
        }

        public function testItSetsGlobalTag(): void
        {
            $span = $this->mock(SpanFake::class, function (MockInterface $mock) {
                $mock->shouldReceive('setTag')->with('test', 'test')->once();
            });
            $tracer = $this->mock(TracerFake::class, function (MockInterface $mock) use ($span) {
                $mock->shouldReceive('getSafeRootSpan')->andReturn($span)->once();
            });
            $metrics = new MetricsService($tracer);
            $metrics->setGlobalTag('test', 'test');
        }

        public function testItSetsGlobalTagNested(): void
        {
            $span = $this->mock(SpanFake::class, function (MockInterface $mock) {
                $mock->shouldReceive('setTag')->with('test.test1.test2', 'test3')->once();
            });
            $tracer = $this->mock(TracerFake::class, function (MockInterface $mock) use ($span) {
                $mock->shouldReceive('getSafeRootSpan')->andReturn($span)->once();
            });

            $metrics = new MetricsService($tracer);
            $metrics->setGlobalTag('test', ['test1' => ['test2' => 'test3']]);
        }

        public function testItGetsGlobalTag(): void
        {
            $span = $this->mock(SpanFake::class, function (MockInterface $mock) {
                $mock->tags['test'] = 'test';
            });
            $tracer = $this->mock(TracerFake::class, function (MockInterface $mock) use ($span) {
                $mock->shouldReceive('getSafeRootSpan')->andReturn($span)->once();
            });
            $metrics = new MetricsService($tracer);
            $this->assertEquals('test', $metrics->getGlobalTag('test'));
        }

        public function testItUnsetsGlobalTag(): void
        {
            $span = $this->mock(SpanFake::class, function (MockInterface $mock) {
                $mock->tags['test'] = 'test';
            });
            $tracer = $this->mock(TracerFake::class, function (MockInterface $mock) use ($span) {
                $mock->shouldReceive('getSafeRootSpan')->andReturn($span)->once();
            });
            $metrics = new MetricsService($tracer);
            $metrics->unsetGlobalTag('test');
            $this->assertFalse(isset($span->tags['test']));
        }

        public function testItHasGlobalTag(): void
        {
            $span = $this->mock(SpanFake::class, function (MockInterface $mock) {
                $mock->tags['test'] = 'test';
            });
            $tracer = $this->mock(TracerFake::class, function (MockInterface $mock) use ($span) {
                $mock->shouldReceive('getSafeRootSpan')->andReturn($span)->twice();
            });
            $metrics = new MetricsService($tracer);
            $this->assertEquals(true, $metrics->hasGlobalTag('test'));
            $this->assertEquals(false, $metrics->hasGlobalTag('not-test'));
        }

        public function testItDoesNotHaveGlobalTagWithoutSpan(): void
        {
            $tracer = $this->mock(TracerFake::class, function (MockInterface $mock) {
                $mock->shouldReceive('getSafeRootSpan')->andReturn(null)->once();
            });
            $metrics = new MetricsService($tracer);
            $this->assertEquals(false, $metrics->hasGlobalTag('not-test'));
        }

        public function testItAddsGlobalMetric(): void
        {
            $span = $this->mock(SpanFake::class, function (MockInterface $mock) {
                $mock->shouldReceive('setMetric')->with('test', 1)->once();
            });
            $tracer = $this->mock(TracerFake::class, function (MockInterface $mock) use ($span) {
                $mock->shouldReceive('getSafeRootSpan')->andReturn($span)->once();
            });
            $metrics = new MetricsService($tracer);
            $metrics->setGlobalMetric('test', 1);
        }

        public function testItAddsGlobalMetricNested(): void
        {
            $span = $this->mock(SpanFake::class, function (MockInterface $mock) {
                $mock->shouldReceive('setMetric')->with('test.test1.test2', 1)->once();
            });
            $tracer = $this->mock(TracerFake::class, function (MockInterface $mock) use ($span) {
                $mock->shouldReceive('getSafeRootSpan')->andReturn($span)->once();
            });
            $metrics = new MetricsService($tracer);
            $metrics->setGlobalMetric('test', ['test1' => ['test2' => 1]]);
        }

        public function testItIgnoresFalseGlobalMetric(): void
        {
            $span = $this->mock(SpanFake::class, function (MockInterface $mock) {
                $mock->shouldReceive('setMetric')->never();
            });
            $tracer = $this->mock(TracerFake::class, function (MockInterface $mock) use ($span) {
                $mock->shouldReceive('getSafeRootSpan')->andReturn($span)->once();
            });
            $metrics = new MetricsService($tracer);
            $metrics->setGlobalMetric('test', false);
        }

        public function testItSetsErrorException(): void
        {
            $e = new Exception();
            $span = $this->mock(SpanFake::class, function (MockInterface $mock) use ($e) {
                $mock->shouldReceive('setError')->with($e)->once();
            });
            $tracer = $this->mock(TracerFake::class, function (MockInterface $mock) use ($span) {
                $mock->shouldReceive('getActiveSpan')->andReturn($span)->once();
            });

            $metrics = new MetricsService($tracer);
            $metrics->setError($e);
        }

        public function testItSetsErrorExceptionType(): void
        {
            $e = new Exception();
            $span = $this->mock(SpanFake::class, function (MockInterface $mock) use ($e) {
                $mock->shouldReceive('setError')->with($e)->once();
            });
            $tracer = $this->mock(TracerFake::class, function (MockInterface $mock) use ($span) {
                $mock->shouldReceive('getActiveSpan')->andReturn($span)->once();
            });

            $metrics = new MetricsService($tracer);
            $metrics->setError($e, 'test');

            $this->assertEquals('test', $span->tags[MetricsService::ERROR_TYPE]);
        }

        public function testItSetsErrorStringType(): void
        {
            $span = $this->mock(SpanFake::class, function (MockInterface $mock) {
                $mock->shouldReceive('setRawError')->with('error', 'test')->once();
            });
            $tracer = $this->mock(TracerFake::class, function (MockInterface $mock) use ($span) {
                $mock->shouldReceive('getActiveSpan')->andReturn($span)->once();
            });

            $metrics = new MetricsService($tracer);
            $metrics->setError('error', 'test');
        }

        public function testItSetsErrorString(): void
        {
            $span = $this->mock(SpanFake::class, function (MockInterface $mock) {
                $mock->shouldReceive('setRawError')->never();
            });
            $tracer = $this->mock(TracerFake::class, function (MockInterface $mock) use ($span) {
                $mock->shouldReceive('getActiveSpan')->andReturn($span)->once();
            });

            $metrics = new MetricsService($tracer);
            $metrics->setError('error');

            $this->assertTrue($span->hasError);
            $this->assertEquals($span->tags[MetricsService::ERROR_MSG], 'error');
            $this->assertIsString($span->tags[MetricsService::ERROR_STACK]);
            $this->assertStringStartsWith('#0', $span->tags[MetricsService::ERROR_STACK]);
        }

        public function testItSetsErrorBoolean(): void
        {
            $span = $this->mock(SpanFake::class, function (MockInterface $mock) {
                $mock->shouldReceive('setRawError')->never();
            });
            $tracer = $this->mock(TracerFake::class, function (MockInterface $mock) use ($span) {
                $mock->shouldReceive('getActiveSpan')->andReturn($span)->once();
            });

            $metrics = new MetricsService($tracer);
            $metrics->setError(true);

            $this->assertTrue($span->hasError);
            $this->assertFalse(isset($span->tags[MetricsService::ERROR_MSG]));
            $this->assertIsString($span->tags[MetricsService::ERROR_STACK]);
            $this->assertStringStartsWith('#0', $span->tags[MetricsService::ERROR_STACK]);
        }

        public function testItSetsTag(): void
        {
            $span = $this->mock(SpanFake::class, function (MockInterface $mock) {
                $mock->shouldReceive('setTag')->with('test', 'test')->once();
            });
            $tracer = $this->mock(TracerFake::class, function (MockInterface $mock) use ($span) {
                $mock->shouldReceive('getActiveSpan')->andReturn($span)->once();
            });

            $metrics = new MetricsService($tracer);
            $metrics->setTag('test', 'test');
        }

        public function testItSetsTagNested(): void
        {
            $span = $this->mock(SpanFake::class, function (MockInterface $mock) {
                $mock->shouldReceive('setTag')->with('test.test1.test2', 'test3')->once();
            });
            $tracer = $this->mock(TracerFake::class, function (MockInterface $mock) use ($span) {
                $mock->shouldReceive('getActiveSpan')->andReturn($span)->once();
            });

            $metrics = new MetricsService($tracer);
            $metrics->setTag('test', ['test1' => ['test2' => 'test3']]);
        }

        public function testItAddsMetric(): void
        {
            $span = $this->mock(SpanFake::class, function (MockInterface $mock) {
                $mock->shouldReceive('setMetric')->with('test', 1)->once();
            });
            $tracer = $this->mock(TracerFake::class, function (MockInterface $mock) use ($span) {
                $mock->shouldReceive('getActiveSpan')->andReturn($span)->once();
            });
            $metrics = new MetricsService($tracer);
            $metrics->setMetric('test', 1);
        }

        public function testItAddsMetricNested(): void
        {
            $span = $this->mock(SpanFake::class, function (MockInterface $mock) {
                $mock->shouldReceive('setMetric')->with('test.test1.test2', 1)->once();
            });
            $tracer = $this->mock(TracerFake::class, function (MockInterface $mock) use ($span) {
                $mock->shouldReceive('getActiveSpan')->andReturn($span)->once();
            });
            $metrics = new MetricsService($tracer);
            $metrics->setMetric('test', ['test1' => ['test2' => 1]]);
        }

        public function testItIgnoresFalseMetric(): void
        {
            $span = $this->mock(SpanFake::class, function (MockInterface $mock) {
                $mock->shouldReceive('setMetric')->never();
            });
            $tracer = $this->mock(TracerFake::class, function (MockInterface $mock) use ($span) {
                $mock->shouldReceive('getActiveSpan')->andReturn($span)->once();
            });
            $metrics = new MetricsService($tracer);
            $metrics->setMetric('test', false);
        }

        public function testItStartsSpan(): void
        {
            $span = $this->mock(SpanFake::class, function (MockInterface $mock) {
            });
            $tracer = $this->mock(TracerFake::class, function (MockInterface $mock) use ($span) {
                $mock->shouldReceive('startActiveSpan')->with('test', [])->once();
                $mock->shouldReceive('getActiveSpan')->andReturn($span)->once();
            });
            $metrics = new MetricsService($tracer);
            $newMetrics = $metrics->startSpan('test');

            $this->assertNotEquals($span, $this->getProtectedAttribute($metrics, 'span'));
            $this->assertEquals($span, $this->getProtectedAttribute($newMetrics, 'span'));
        }

        public function testItEndsSpan(): void
        {
            $span = $this->mock(SpanFake::class, function (MockInterface $mock) {
                $mock->shouldReceive('isFinished')->andReturnFalse()->once();
                $mock->shouldReceive('finish')->once();
            });
            $tracer = $this->mock(TracerFake::class, function (MockInterface $mock) use ($span) {
                $mock->shouldReceive('startActiveSpan')->with('test', [])->once();
                $mock->shouldReceive('getActiveSpan')->andReturn($span)->once();
            });
            $metrics = new MetricsService($tracer);
            $newMetrics = $metrics->startSpan('test');

            $newMetrics->endSpan();
        }

        public function testItEndsSpanActive(): void
        {
            $span = $this->mock(SpanFake::class, function (MockInterface $mock) {
                $mock->shouldReceive('isFinished')->andReturnFalse()->once();
                $mock->shouldReceive('finish')->once();
            });
            $tracer = $this->mock(TracerFake::class, function (MockInterface $mock) use ($span) {
                $mock->shouldReceive('startActiveSpan')->with('test', [])->never();
                $mock->shouldReceive('getActiveSpan')->andReturn($span)->once();
            });
            $metrics = new MetricsService($tracer);
            $metrics->endSpan();
        }

        public function testItTraces(): void
        {
            $span = $this->mock(SpanFake::class, function (MockInterface $mock) {
                $mock->shouldReceive('isFinished')->once()->andReturnFalse();
                $mock->shouldReceive('finish')->once();
            });
            $tracer = $this->mock(TracerFake::class, function (MockInterface $mock) use ($span) {
                $mock->shouldReceive('startActiveSpan')->with('test', [])->once()->andReturn($span);
                $mock->shouldReceive('getActiveSpan')->andReturn($span)->once();
            });

            $metrics = new MetricsService($tracer);
            $metrics->trace('test', function (MetricsService $metrics) {
            });
        }

        public function testItTracesWithException(): void
        {
            $e = new Exception();
            $span = $this->mock(SpanFake::class, function (MockInterface $mock) use ($e) {
                $mock->shouldReceive('isFinished')->once()->andReturnFalse();
                $mock->shouldReceive('finish')->once();
                $mock->shouldReceive('setError')->with($e)->once();
            });
            $tracer = $this->mock(TracerFake::class, function (MockInterface $mock) use ($span) {
                $mock->shouldReceive('startActiveSpan')->with('test', [])->once()->andReturn($span);
                $mock->shouldReceive('getActiveSpan')->andReturn($span)->once();
            });

            $metrics = new MetricsService($tracer);
            $this->expectException(Exception::class);
            $metrics->trace('test', function (MetricsService $metrics) use ($e) {
                throw $e;
            });
        }

        public function testItTracesWithCallback(): void
        {
            $span = $this->mock(SpanFake::class, function (MockInterface $mock) {
                $mock->shouldReceive('isFinished')->once()->andReturnFalse();
                $mock->shouldReceive('finish')->once();
            });
            $tracer = $this->mock(TracerFake::class, function (MockInterface $mock) use ($span) {
                $mock->shouldReceive('startActiveSpan')->with('test', [])->once()->andReturn($span);
                $mock->shouldReceive('getActiveSpan')->andReturn($span)->once();
            });

            $metrics = new MetricsService($tracer);
            $metrics->trace('test', function (MetricsService $metrics) {
                return 'test';
            }, function (MetricsService $metrics, $result) {
                $this->assertEquals('test', $result);
            });
        }

        public function testItTracesWithCallbackException(): void
        {
            $e = new Exception();
            $span = $this->mock(SpanFake::class, function (MockInterface $mock) use ($e) {
                $mock->shouldReceive('isFinished')->once()->andReturnFalse();
                $mock->shouldReceive('finish')->once();
                $mock->shouldReceive('setError')->with($e)->once();
            });
            $tracer = $this->mock(TracerFake::class, function (MockInterface $mock) use ($span) {
                $mock->shouldReceive('startActiveSpan')->with('test', [])->once()->andReturn($span);
                $mock->shouldReceive('getActiveSpan')->andReturn($span)->once();
            });

            $metrics = new MetricsService($tracer);
            $this->expectException(Exception::class);
            $metrics->trace('test', function (MetricsService $metrics) use ($e) {
                throw $e;
            }, function (MetricsService $metrics, $result, $e) {
                $this->assertEquals(null, $result);
                $this->assertInstanceOf(Exception::class, $e);
            });
        }
    }
}

namespace DDTrace\Contracts {
    if (!\interface_exists('DDTrace\Contracts\Span')) {
        interface Span
        {
        }
    }
    if (!\interface_exists('DDTrace\Contracts\Tracer')) {
        interface Tracer
        {
        }
    }
}
