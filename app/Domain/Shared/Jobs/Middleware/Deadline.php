<?php
declare(strict_types=1);

namespace App\Domain\Shared\Jobs\Middleware;

use App\Domain\Shared\Contracts\Jobs\Middleware;
use App\Domain\Shared\Exceptions\DeadlineExceededException;
use App\Domain\Shared\Jobs\BaseJob;
use App\Domain\Shared\Jobs\Traits\HasDeadline;
use Illuminate\Support\Facades\Date;
use Log;

class Deadline implements Middleware
{
    /** @psalm-suppress UndefinedDocblockClass */
    public function handle(BaseJob $job, callable $next): ?Middleware
    {
        /** @var HasDeadline $job */
        if ($job->hasDeadline() && Date::now()->greaterThanOrEqualTo($job->getDeadline())) {
            Log::error('[queue] Deadline exceeded', [
                'type' => 'job',
                'job' => $job->getName(),
                'uuid' => $job->metadata['uuid'],
                'guid' => $job->metadata['guid'],
                'attempts' => $job->attempts(),
                'stack_trace' => array_slice(debug_backtrace(DEBUG_BACKTRACE_IGNORE_ARGS), 0, 5),
                'deadline' => $job->getDeadline()->format(DATE_ATOM),
                'current_time' => Date::now()->format(DATE_ATOM),
            ]);

            $job->fail(new DeadlineExceededException());

            return null;
        }

        return $next($job);
    }
}
