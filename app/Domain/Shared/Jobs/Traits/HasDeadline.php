<?php
declare(strict_types=1);

namespace App\Domain\Shared\Jobs\Traits;

use App\Domain\Shared\Jobs\Middleware\Deadline;

/**
 * @mixin \App\Domain\Shared\Jobs\BaseJob
 */
trait HasDeadline
{
    use HasMiddleware;

    protected \DateTimeInterface $retryUntil;

    public function deadline(\DateTimeInterface $dateTime): void
    {
        $this->retryUntil = $dateTime;
    }

    public function hasDeadline(): bool
    {
        return isset($this->retryUntil);
    }

    public function getDeadline(): \DateTimeInterface
    {
        return $this->retryUntil;
    }

    /**
     * @return array<Deadline>
     */
    public function middlewareHasDeadline(): array
    {
        return [
            resolve(Deadline::class),
        ];
    }
}
