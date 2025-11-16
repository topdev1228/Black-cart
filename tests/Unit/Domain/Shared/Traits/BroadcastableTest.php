<?php
declare(strict_types=1);

namespace Tests\Unit\Domain\Shared\Traits;

use App\Domain\Shared\Traits\Broadcastable;
use Illuminate\Broadcasting\Channel;
use Illuminate\Contracts\Support\Arrayable;
use Illuminate\Contracts\Support\Jsonable;
use Tests\TestCase;

class BroadcastableTest extends TestCase
{
    public function testItBroadcastsWith(): void
    {
        $mock = new class {
            use Broadcastable;

            public $foo = 'bar';
            protected $bar = 'bat';
        };

        $expected = ['foo' => 'bar', 'bar' => 'bat'];
        $expected['__event'] = $mock::class;

        $this->assertEquals($expected, $mock->broadcastWith());
    }

    public function testItBroadcastsWithArrayable(): void
    {
        $mock = new class implements Arrayable {
            use Broadcastable;

            public function toArray(): array
            {
                return [
                    'foo' => 'bar',
                ];
            }
        };

        $expected = $mock->toArray();
        $expected['__event'] = $mock::class;

        $this->assertEquals($expected, $mock->broadcastWith());
    }

    public function testItBroadcastsWithJsonable(): void
    {
        $mock = new class implements Jsonable {
            use Broadcastable;

            public function toJson($options = 0): array
            {
                return [
                    'foo' => 'bar',
                ];
            }
        };

        $expected = $mock->toJson();
        $expected['__event'] = $mock::class;

        $this->assertEquals($expected, $mock->broadcastWith());
    }

    public function testItBroadcastsOn(): void
    {
        $mock = new class {
            use Broadcastable;
        };

        $expected = [
            new Channel($mock::class),
        ];

        $this->assertEquals($expected, $mock->broadcastOn());
    }
}
