<?php
declare(strict_types=1);

namespace App\Domain\Shared\Jobs\Middleware;

use App\Domain\Shared\Contracts\Jobs\Middleware;
use App\Domain\Shared\Facades\AppMetrics;
use App\Domain\Shared\Jobs\BaseJob;
use App\Domain\Shared\Jobs\Traits\HasMetadata;
use App\Domain\Shared\Services\MetricsService;
use Illuminate\Queue\Events\JobExceptionOccurred;
use Illuminate\Support\Facades\Date;

class Logging implements Middleware
{
    public function handle(BaseJob $job, callable $next): ?Middleware
    {
        $started = Date::now();

        $context = [
            'type' => 'job',
            'job' => $job->getName(),
            'stack_trace' => array_slice(debug_backtrace(DEBUG_BACKTRACE_IGNORE_ARGS), 0, 5),
        ];

        $attempts = $job->attempts();
        if (isset(class_uses_recursive($job)[HasMetadata::class])) {
            $context['uuid'] = $job->metadata['uuid'];
            $context['guid'] = $job->metadata['guid'];
            $context['attempts'] = $attempts;
        }

        AppMetrics::setGlobalTag('job', $context);

        $result = AppMetrics::trace($job->getName(), function (MetricsService $metrics) use ($next, $job, $started, $attempts): ?Middleware {
            $context = [];
            $result = $next($job);

            $context['stack_trace'] = array_slice(debug_backtrace(DEBUG_BACKTRACE_IGNORE_ARGS), 0, 5);
            $context['duration'] = round($started->floatDiffInRealSeconds(Date::now()), 5) . 's';
            $context['attempts'] = $job->attempts();

            $metrics->setTag('job', $context);

            if (!$job->job?->hasFailed()) {
                $action = 'completed';
                if ($attempts < $context['attempts']) {
                    $action = 'redispatched';
                }

                $metrics->setTag('job.action', $action);
            } else {
                $metrics->setError('Job failed', JobExceptionOccurred::class);
            }

            return $result;
        });

        return $result;
    }
}
