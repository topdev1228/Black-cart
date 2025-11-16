<?php
declare(strict_types=1);

namespace Tests\Unit\Domain\Shared\Values;

use Illuminate\Database\Eloquent\Factories\Sequence;
use Illuminate\Validation\ValidationException;
use Spatie\LaravelData\DataCollection;
use Tests\Fixtures\Values\Collections\TestValueWithCollectionCollection;
use Tests\Fixtures\Values\TestValue;
use Tests\Fixtures\Values\TestValueWithCollection;
use Tests\Fixtures\Values\TestValueWithOptional;
use Tests\Fixtures\Values\TestValueWithValidation;
use Tests\TestCase;

class FactoryTest extends TestCase
{
    public function testItCreatesValue(): void
    {
        $value = TestValue::builder()->create();
        $this->assertInstanceOf(TestValue::class, $value);
        $this->assertEquals('test', $value->name);
    }

    public function testItFailsToCreateValueWithFactoryState(): void
    {
        $value = TestValue::builder(['name' => 'factory'])->create();
        $this->assertInstanceOf(TestValue::class, $value);
        $this->assertEquals('factory', $value->name);
    }

    public function testItCreatesValueWithCreateState(): void
    {
        $value = TestValue::builder()->create(['name' => 'create']);
        $this->assertInstanceOf(TestValue::class, $value);
        $this->assertEquals('create', $value->name);
    }

    public function testItCreatesValueWithStateArray(): void
    {
        $value = TestValue::builder()->state(['name' => 'state'])->create();
        $this->assertInstanceOf(TestValue::class, $value);
        $this->assertEquals('state', $value->name);
    }

    public function testItCreatesValueWithFactoryStateCreate(): void
    {
        $value = TestValue::builder(['name' => 'factory'])->state(['name' => 'state'])->create(['name' => 'create']);
        $this->assertInstanceOf(TestValue::class, $value);
        $this->assertEquals('create', $value->name);
    }

    public function testItCreatesValues(): void
    {
        $collection = TestValue::builder(5)->create();
        $this->assertInstanceOf(DataCollection::class, $collection);
        $this->assertEquals(5, $collection->count());
        foreach ($collection as $value) {
            $this->assertInstanceOf(TestValue::class, $value);
            $this->assertEquals('test', $value->name);
        }
    }

    public function testItCreatesValuesWithCount(): void
    {
        $collection = TestValue::builder()->count(5)->create();
        $this->assertInstanceOf(DataCollection::class, $collection);
        $this->assertEquals(5, $collection->count());
        foreach ($collection as $value) {
            $this->assertInstanceOf(TestValue::class, $value);
            $this->assertEquals('test', $value->name);
        }
    }

    public function testItCreatesValuesWithCollection(): void
    {
        $collection = TestValueWithCollection::builder(5)->create();
        $this->assertInstanceOf(TestValueWithCollectionCollection::class, $collection);
        $this->assertEquals(5, $collection->count());
        foreach ($collection as $value) {
            $this->assertInstanceOf(TestValueWithCollection::class, $value);
            $this->assertEquals('test', $value->name);
        }
    }

    public function testItCreatesValuesWithoutCollection(): void
    {
        $collection = TestValue::builder(5)->create();
        $this->assertInstanceOf(DataCollection::class, $collection);
        $this->assertEquals(5, $collection->count());
        foreach ($collection as $value) {
            $this->assertInstanceOf(TestValue::class, $value);
            $this->assertEquals('test', $value->name);
        }
    }

    public function testItCreatesValuesWithSequence(): void
    {
        $collection = TestValueWithCollection::builder(5)->state(new Sequence(['name' => 'a'], ['name' => 'b']))->create();
        $this->assertInstanceOf(TestValueWithCollectionCollection::class, $collection);
        $this->assertEquals(5, $collection->count());
        foreach ($collection as $value) {
            $this->assertInstanceOf(TestValueWithCollection::class, $value);
        }

        $values = $collection->values();
        $this->assertEquals('a', $values[0]->name);
        $this->assertEquals('b', $values[1]->name);
        $this->assertEquals('a', $values[2]->name);
        $this->assertEquals('b', $values[3]->name);
        $this->assertEquals('a', $values[4]->name);
    }

    public function testItCreatesValuesWithSequenceFactory(): void
    {
        $collection = TestValueWithCollection::builder(5, ['name' => 'factory'])->state(new Sequence(['name' => 'a'], ['name' => 'b']))->create();
        $this->assertInstanceOf(TestValueWithCollectionCollection::class, $collection);
        $this->assertEquals(5, $collection->count());
        foreach ($collection as $value) {
            $this->assertInstanceOf(TestValueWithCollection::class, $value);
        }

        $values = $collection->values();
        $this->assertEquals('a', $values[0]->name);
        $this->assertEquals('b', $values[1]->name);
        $this->assertEquals('a', $values[2]->name);
        $this->assertEquals('b', $values[3]->name);
        $this->assertEquals('a', $values[4]->name);
    }

    public function testItCreatesValuesWithSequenceCreate(): void
    {
        $collection = TestValueWithCollection::builder(5)->state(new Sequence(['name' => 'a'], ['name' => 'b']))->create(['name' => 'create']);
        $this->assertInstanceOf(TestValueWithCollectionCollection::class, $collection);
        $this->assertEquals(5, $collection->count());
        foreach ($collection as $value) {
            $this->assertInstanceOf(TestValueWithCollection::class, $value);
        }

        $values = $collection->values();
        $this->assertEquals('a', $values[0]->name);
        $this->assertEquals('b', $values[1]->name);
        $this->assertEquals('a', $values[2]->name);
        $this->assertEquals('b', $values[3]->name);
        $this->assertEquals('a', $values[4]->name);
    }

    public function testItCreatesValuesWithSequenceFactoryCreate(): void
    {
        $collection = TestValueWithCollection::builder(5, ['name' => 'factory'])->state(new Sequence(['name' => 'a'], ['name' => 'b']))->create(['name' => 'create']);
        $this->assertInstanceOf(TestValueWithCollectionCollection::class, $collection);
        $this->assertEquals(5, $collection->count());
        foreach ($collection as $value) {
            $this->assertInstanceOf(TestValueWithCollection::class, $value);
        }

        $values = $collection->values();
        $this->assertEquals('a', $values[0]->name);
        $this->assertEquals('b', $values[1]->name);
        $this->assertEquals('a', $values[2]->name);
        $this->assertEquals('b', $values[3]->name);
        $this->assertEquals('a', $values[4]->name);
    }

    public function testItCreatesEmptyValue(): void
    {
        $value = TestValueWithOptional::builder()->create(['name' => 'testing']);
        $this->assertEquals('testing', $value->name);
        $this->assertEquals('testing', $value->nickname);

        $value = TestValueWithOptional::builder()->empty()->create(['name' => 'testing']);
        $this->assertEquals('testing', $value->name);
        $this->assertNull($value->nickname);
    }

    public function testItValidatesValues(): void
    {
        $value = TestValueWithValidation::builder()->validate()->create();
        $this->assertInstanceOf(TestValueWithValidation::class, $value);

        $this->expectException(ValidationException::class);
        $this->expectExceptionMessage('The name field must not be greater than 4 characters.');
        TestValueWithValidation::builder()->validate()->create(['name' => 'testing']);
    }
}
