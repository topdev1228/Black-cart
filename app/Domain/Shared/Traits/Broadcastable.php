<?php
declare(strict_types=1);

namespace App\Domain\Shared\Traits;

use function get_object_vars;
use Illuminate\Broadcasting\Channel;
use Illuminate\Contracts\Support\Arrayable;
use Illuminate\Contracts\Support\Jsonable;

trait Broadcastable
{
    public function broadcastWith(): array
    {
        $data = match (true) {
            $this instanceof Arrayable => $this->toArray(),
            $this instanceof Jsonable => $this->toJson(),
            default => get_object_vars($this),
        };

        $data['__event'] = static::class;

        return $data;
    }

    /**
     * Get the channels the event should broadcast on.
     *
     * @return array<int, Channel>
     */
    public function broadcastOn(): array
    {
        return [
            new Channel(static::class),
        ];
    }
}
