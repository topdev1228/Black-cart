<?php
declare(strict_types=1);

namespace App\Domain\Shared\Jobs\Traits;

use App\Domain\Shared\Contracts\Jobs\Middleware;
use App\Domain\Shared\Jobs\BaseJob;
use function class_basename;
use function class_uses_recursive;
use function get_parent_class;
use function method_exists;

/**
 * @mixin BaseJob
 */
trait HasMiddleware
{
    /**
     * @psalm-suppress ParentNotFound
     * @return array<Middleware>
     */
    public function middleware(): array
    {
        $middleware = [];
        $parent = get_parent_class($this);
        if ($parent !== BaseJob::class) {
            if (method_exists(parent::class, 'middleware')) {
                $middleware = parent::middleware();
            }
        }

        foreach (class_uses_recursive($this) as $trait) {
            $method = 'middleware' . class_basename($trait);
            if (method_exists($trait, $method)) {
                $middleware = array_merge($middleware, $this->{$method}());
            }
        }

        return $middleware;
    }
}
