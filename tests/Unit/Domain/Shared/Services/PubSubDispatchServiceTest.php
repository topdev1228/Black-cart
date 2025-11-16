<?php
declare(strict_types=1);

namespace Tests\Unit\Domain\Shared\Services;

use App\Domain\Shared\Exceptions\InvalidListenerException;
use App\Domain\Shared\Services\PubSubDispatchService;
use App\Domain\Shared\Traits\Broadcastable;
use App\Domain\Shared\Values\PubSubMessageEnvelope;
use App\Domain\Stores\Events\StoreCreated;
use App\Domain\Stores\Values\Store as StoresStoreValue;
use Illuminate\Support\Collection;
use Tests\Fixtures\Values\TestValue;
use Tests\TestCase;

class PubSubDispatchServiceTest extends TestCase
{
    public function testItDispatchesListenerWithNoArguments(): void
    {
        $this->app->singleton('App\Domain\Tests\Listeners\MockListener', fn () => new class($this) {
            public function __construct(protected PubSubDispatchServiceTest $test)
            {
            }

            public function handle(): void
            {
                $this->test->assertTrue(true);
            }
        });

        $event = new StoreCreated(StoresStoreValue::builder()->create());

        $message = PubSubMessageEnvelope::builder()->data($event->broadcastWith())->create([
            'subscription' => 'shopify-tests-mock',
        ]);

        resolve(PubSubDispatchService::class)->dispatch($message);
    }

    public function testItDispatchesListenerWithCollection(): void
    {
        $this->app->singleton('App\Domain\Tests\Listeners\MockListener', fn () => new class($this) {
            public function __construct(protected PubSubDispatchServiceTest $test)
            {
            }

            public function handle(Collection $data): void
            {
                $this->test->assertCount(18, $data);
            }
        });

        $message = PubSubMessageEnvelope::builder()->shopifyWebhook()->create([
            'subscription' => 'shopify-tests-mock',
        ]);

        resolve(PubSubDispatchService::class)->dispatch($message);
    }

    public function testItDispatchesListenerWithValue(): void
    {
        $this->app->singleton('App\Domain\Tests\Listeners\MockListener', fn () => new class($this) {
            public function __construct(protected PubSubDispatchServiceTest $test)
            {
            }

            public function handle(TestValue $value): void
            {
                $this->test->assertEquals('test', $value->name);
                $this->test->assertTrue(true);
            }
        });

        $event = new class {
            use Broadcastable;

            public $name = 'test';
        };

        $message = PubSubMessageEnvelope::builder()->data($event->broadcastWith())->create([
            'subscription' => 'shopify-tests-mock',
        ]);

        resolve(PubSubDispatchService::class)->dispatch($message);
    }

    public function testItDispatchesListenerWithArray(): void
    {
        $this->app->singleton('App\Domain\Tests\Listeners\MockListener', fn () => new class($this) {
            public function __construct(protected PubSubDispatchServiceTest $test)
            {
            }

            public function handle(array $value): void
            {
                $this->test->assertEquals('test', $value['name']);
                $this->test->assertEquals('bar', $value['foo']);
                $this->test->assertTrue(true);
            }
        });

        $event = new class {
            use Broadcastable;

            public $name = 'test';
            public $foo = 'bar';
        };

        $message = PubSubMessageEnvelope::builder([
            'subscription' => 'shopify-tests-mock',
        ])->data($event->broadcastWith())->create();

        resolve(PubSubDispatchService::class)->dispatch($message);
    }

    public function testItDispatchesListenerWithAttributesCollection(): void
    {
        $this->app->singleton('App\Domain\Tests\Listeners\MockListener', fn () => new class($this) {
            public function __construct(protected PubSubDispatchServiceTest $test)
            {
            }

            public function handle(array $value, Collection $attributes): void
            {
                $this->test->assertEquals('test', $value['name']);
                $this->test->assertEquals('bar', $value['foo']);

                $this->test->assertEquals('test', $attributes['event']);
                $this->test->assertEquals('test', $attributes['uuid']);
                $this->test->assertEquals('example.com', $attributes['domain']);

                $this->test->assertTrue(true);
            }
        });

        $event = new class {
            use Broadcastable;

            public $name = 'test';
            public $foo = 'bar';
        };

        $message = PubSubMessageEnvelope::builder()
            ->data($event->broadcastWith())
            ->attributes(['event' => 'test', 'uuid' => 'test', 'domain' => 'example.com'])
            ->create([
                'subscription' => 'shopify-tests-mock',
            ]);

        resolve(PubSubDispatchService::class)->dispatch($message);
    }

    public function testItDispatchesListenerWithAttributesArray(): void
    {
        $this->app->singleton('App\Domain\Tests\Listeners\MockListener', fn () => new class($this) {
            public function __construct(protected PubSubDispatchServiceTest $test)
            {
            }

            public function handle(array $value, array $attributes): void
            {
                $this->test->assertEquals('test', $value['name']);
                $this->test->assertEquals('bar', $value['foo']);

                $this->test->assertEquals('test', $attributes['event']);
                $this->test->assertEquals('test', $attributes['uuid']);
                $this->test->assertEquals('example.com', $attributes['domain']);

                $this->test->assertTrue(true);
            }
        });

        $event = new class {
            use Broadcastable;

            public $name = 'test';
            public $foo = 'bar';
        };

        $message = PubSubMessageEnvelope::builder()
            ->data($event->broadcastWith())
            ->attributes(['event' => 'test', 'uuid' => 'test', 'domain' => 'example.com'])
            ->create([
                'subscription' => 'shopify-tests-mock',
            ]);

        resolve(PubSubDispatchService::class)->dispatch($message);
    }

    public function testItDispatchesListenerWithAttributesValue(): void
    {
        $this->app->singleton('App\Domain\Tests\Listeners\MockListener', fn () => new class($this) {
            public function __construct(protected PubSubDispatchServiceTest $test)
            {
            }

            public function handle(array $value, TestValue $attributes): void
            {
                $this->test->assertEquals('testing', $attributes->name);

                $this->test->assertTrue(true);
            }
        });

        $event = new class {
            use Broadcastable;

            public $name = 'test';
            public $foo = 'bar';
        };

        $message = PubSubMessageEnvelope::builder()
            ->data($event->broadcastWith())
            ->attributes(['name' => 'testing'])
            ->create([
                'subscription' => 'shopify-tests-mock',
            ]);

        resolve(PubSubDispatchService::class)->dispatch($message);
    }

    public function testItErrorsOnInvalidListener(): void
    {
        $message = PubSubMessageEnvelope::builder([
            'subscription' => 'shopify-tests-mock',
        ])->create();

        $this->expectException(InvalidListenerException::class);
        $this->expectExceptionMessage('Invalid listener: App\Domain\Tests\Listeners\MockListener');
        resolve(PubSubDispatchService::class)->dispatch($message);
    }
}
