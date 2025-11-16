<?php
declare(strict_types=1);

namespace Tests\Unit\Domain\Shared\Jobs\Middleware;

use Log;
use Tests\Fixtures\Jobs\LoggingJob;
use Tests\TestCase;

class LoggingTest extends TestCase
{
    public function testItLogs(): void
    {
        Log::shouldReceive('debug')->with('[test] Handle method called')->once();

        LoggingJob::dispatch();
    }

    public function testItLogsRedispatch(): void
    {
        Log::shouldReceive('debug')->with('[test] Handle method called')->twice();
        LoggingJob::dispatch(true);
    }
}
