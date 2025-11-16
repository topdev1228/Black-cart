<?php
declare(strict_types=1);

namespace Tests\Unit\Domain\Shared\Providers {
    use App\Domain\Shared\Services\MetricsService;
    use Tests\TestCase;

    class MetricsServiceProviderTest extends TestCase
    {
        public function testItCreatesServices(): void
        {
            $metrics = app(MetricsService::class);
            $this->assertInstanceOf(MetricsService::class, $metrics);
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
