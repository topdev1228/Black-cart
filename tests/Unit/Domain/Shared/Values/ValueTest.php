<?php
declare(strict_types=1);

namespace Tests\Unit\Domain\Shared\Values;

use App\Domain\Shared\Exceptions\UndefinedPropertyException;
use App\Domain\Shared\Values\Value;
use Error;
use Illuminate\Validation\ValidationException;
use Tests\TestCase;

class ValueTest extends TestCase
{
    public function testItHidesValue(): void
    {
        $value = new class('foo', 'bar', 'bat') extends Value {
            protected array $hidden = ['bar'];

            public function __construct(
                public string $foo,
                public string $bar,
                public string $bat,
            ) {
            }
        };

        $values = $value->toArray();
        $this->assertEquals(['foo' => 'foo', 'bat' => 'bat'], $values);
    }

    public function testItHidesValues(): void
    {
        $value = new class('foo', 'bar', 'bat') extends Value {
            protected array $hidden = ['bar', 'bat'];

            public function __construct(
                public string $foo,
                public string $bar,
                public string $bat,
            ) {
            }
        };

        $values = $value->toArray();
        $this->assertEquals(['foo' => 'foo'], $values);
    }

    public function testItValidatesHas(): void
    {
        $value = new class('foo', 'bar', 'bat') extends Value {
            public function __construct(
                public string $foo,
                public string $bar,
                public string $bat,
            ) {
            }
        };

        $this->assertTrue($value->has('foo'));
        $this->assertTrue($value->has('foo', 'bar'));
        $this->assertTrue($value->has('foo', 'bar', 'bat'));
    }

    public function testItValidatesHasWithHidden(): void
    {
        $value = new class('foo', 'bar', 'bat') extends Value {
            protected array $hidden = ['bar'];

            public function __construct(
                public string $foo,
                public string $bar,
                public string $bat,
            ) {
            }
        };

        $this->assertTrue($value->has('foo'));
        $this->assertTrue($value->has('foo', 'bar'));
        $this->assertTrue($value->has('foo', 'bar', 'bat'));
    }

    public function testItValidatesHasWithMultipleHidden(): void
    {
        $value = new class('foo', 'bar', 'bat') extends Value {
            protected array $hidden = ['foo', 'bar', 'bat'];

            public function __construct(
                public string $foo,
                public string $bar,
                public string $bat,
            ) {
            }
        };

        $this->assertTrue($value->has('foo'));
        $this->assertTrue($value->has('foo', 'bar'));
        $this->assertTrue($value->has('foo', 'bar', 'bat'));
    }

    public function testItValidatesHasWithMissingValue(): void
    {
        $value = new class('foo', 'bar', null) extends Value {
            public function __construct(
                public string $foo,
                public ?string $bar,
                public ?string $bat,
            ) {
            }
        };

        $this->assertTrue($value->has('foo'));
        $this->assertTrue($value->has('foo', 'bar'));

        $this->expectException(ValidationException::class);
        $this->expectExceptionMessage('bat is required.');
        $value->has('foo', 'bar', 'bat');
    }

    public function testItValidatesHasWithMissingValuesOneRequired(): void
    {
        $value = new class('foo', null, null) extends Value {
            public function __construct(
                public string $foo,
                public ?string $bar,
                public ?string $bat,
            ) {
            }
        };

        $this->expectException(ValidationException::class);
        $this->expectExceptionMessage('bar is required.');
        $value->has('foo', 'bar');
    }

    public function testItValidatesHasWithMissingValuesMultipleRequired(): void
    {
        $value = new class('foo', null, null) extends Value {
            public function __construct(
                public string $foo,
                public ?string $bar,
                public ?string $bat,
            ) {
            }
        };

        $this->expectExceptionMessage('bar is required. (and 1 more error)');
        $this->assertTrue($value->has('foo', 'bar', 'baz'));
    }

    public function testItMutatesOnAccess(): void
    {
        $value = new class('foo', 'bar_bat', 'baz') extends Value {
            public function __construct(protected string $foo, protected string $bar_bat, protected string $baz)
            {
            }

            public function foo(): string
            {
                return ucfirst($this->foo);
            }

            public function barBat(): string
            {
                return strtoupper($this->bar_bat);
            }
        };

        $this->assertEquals('Foo', $value->foo);
        $this->assertEquals('BAR_BAT', $value->bar_bat);
        $this->assertEquals('baz', $value->baz);
    }

    public function testItGetsOriginalValues(): void
    {
        $value = new class('foo', 'bar_bat') extends Value {
            public function __construct(protected string $foo, protected string $bar_bat)
            {
            }

            public function foo(): string
            {
                return ucfirst($this->foo);
            }

            public function barBat(): string
            {
                return strtoupper($this->bar_bat);
            }
        };

        $values = $value->getOriginal();

        $this->assertEquals('foo', $values['foo']);
        $this->assertEquals('bar_bat', $values['bar_bat']);
    }

    public function testItGetsOriginalValue(): void
    {
        $value = new class('foo', 'bar_bat') extends Value {
            public function __construct(protected string $foo, protected string $bar_bat)
            {
            }

            public function foo(): string
            {
                return ucfirst($this->foo);
            }

            public function barBat(): string
            {
                return strtoupper($this->bar_bat);
            }
        };

        $this->assertEquals('foo', $value->getOriginal('foo'));
        $this->assertEquals('bing', $value->getOriginal('bing', 'bing'));
        $this->assertNull($value->getOriginal('bing'));
    }

    public function testItSetsProtectedValues(): void
    {
        $value = new class('foo', 'bar_bat') extends Value {
            public function __construct(protected string $foo, protected string $bar_bat)
            {
            }

            public function foo(): string
            {
                return ucfirst($this->foo);
            }

            public function barBat(): string
            {
                return strtoupper($this->bar_bat);
            }
        };

        $value->foo = 'bar';

        $this->assertEquals('Bar', $value->foo);
        $this->assertEquals('bar', $value->getOriginal('foo'));
    }

    public function testItDetectsProtectedValues(): void
    {
        $value = new class('foo', 'bar_bat') extends Value {
            public function __construct(protected string $foo, protected string $bar_bat)
            {
            }

            public function foo(): string
            {
                return ucfirst($this->foo);
            }

            public function barBat(): string
            {
                return strtoupper($this->bar_bat);
            }
        };

        $this->assertTrue(isset($value->foo));
        $this->assertFalse(isset($value->bing));
    }

    public function testWithDoesNotMutate(): void
    {
        $value = new class('foo') extends Value {
            public function __construct(protected string $foo, protected ?string $bar = null)
            {
            }
        };

        $this->assertEquals('foo', $value->foo);
        $this->assertNull($value->bar);

        $value2 = $value->withBar('bar');

        $this->assertEquals('foo', $value->foo);
        $this->assertNull($value->bar);

        $this->assertEquals('foo', $value2->foo);
        $this->assertEquals('bar', $value2->bar);
    }

    public function testWithThrowsOnUnknownProperty(): void
    {
        $value = new class('foo') extends Value {
            public function __construct(protected string $foo, protected ?string $bar = null)
            {
            }
        };

        $this->assertEquals('foo', $value->foo);
        $this->assertNull($value->bar);

        $this->expectException(UndefinedPropertyException::class);
        $this->expectExceptionMessageMatches('/^Property bat does not exist on .*?\.$/');
        $value->withBat('bat');
    }

    public function testThrowsOnUnknownMethod(): void
    {
        $value = new class('foo') extends Value {
            public function __construct(protected string $foo, protected ?string $bar = null)
            {
            }
        };

        $this->expectException(Error::class);
        $this->expectExceptionMessageMatches('/^Call to undefined method .*?::bar\(\)\.$/');
        $value->bar();
    }
}
