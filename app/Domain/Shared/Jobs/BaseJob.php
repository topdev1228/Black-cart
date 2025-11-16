<?php
declare(strict_types=1);

namespace App\Domain\Shared\Jobs;

use App\Domain\Shared\Jobs\Traits\HasCurrentStore;
use App\Domain\Shared\Jobs\Traits\HasDeadline;
use App\Domain\Shared\Jobs\Traits\HasLogging;
use App\Domain\Shared\Jobs\Traits\HasMetadata;
use App\Domain\Shared\Jobs\Traits\Redispatchable;
use Illuminate\Bus\Batchable;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use ReflectionClass;
use RuntimeException;

abstract class BaseJob implements ShouldQueue
{
    use Dispatchable;
    use InteractsWithQueue;
    use SerializesModels;
    use Batchable;
    use Queueable {
        Queueable::onQueue as setQueue;
    }
    use HasDeadline;
    use HasLogging;
    use HasMetadata;
    use HasCurrentStore;
    use Redispatchable {
        Redispatchable::attempts insteadof InteractsWithQueue;
    }

    public function __construct()
    {
        if (app()->environment('testing')) {
            (new ReflectionClass(static::class))->getMethod('handle')->getReturnType()?->getName() !== 'void' && throw new RuntimeException(sprintf('Job "%s" must return void', static::class));
        }

        $this->setMetadata();

        $this->setQueue(config('queue.connections.cloudtasks.queue'));
    }

    public function getName(): string
    {
        return static::class;
    }

    public function onQueue(string $queue): void
    {
        $this->setQueue('shopify-app-workers-' . $queue);
    }
}
