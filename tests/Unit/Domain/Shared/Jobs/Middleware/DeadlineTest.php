<?php
declare(strict_types=1);

namespace Tests\Unit\Domain\Shared\Jobs\Middleware;

use Illuminate\Support\Facades\Date;
use Log;
use Mockery;
use Tests\Fixtures\Jobs\DeadlineJob;
use Tests\TestCase;

class DeadlineTest extends TestCase
{
    public function testItExecutesWithinDeadline(): void
    {
        Date::setTestNow();

        Log::shouldReceive('error')->never();
        Log::shouldReceive('debug')->with('[test] Handle method called')->once();

        DeadlineJob::dispatch(now()->addSeconds(60));
    }

    public function testItFailsWhenDeadlineExceeded(): void
    {
        Date::setTestNow();

        Log::shouldReceive('error')->with('[queue] Deadline exceeded', Mockery::type('array'))->once();
        Log::shouldReceive('debug')->with('[test] Handle method called')->never();

        DeadlineJob::dispatch(now()->subSeconds(60));
    }
}
