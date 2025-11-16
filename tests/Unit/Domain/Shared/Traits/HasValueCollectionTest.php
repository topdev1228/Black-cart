<?php
declare(strict_types=1);

namespace Tests\Unit\Domain\Shared\Traits;

use Spatie\LaravelData\DataCollection;
use Tests\Fixtures\Values\Collections\TestValueWithCollectionCollection;
use Tests\Fixtures\Values\TestValue;
use Tests\Fixtures\Values\TestValueWithCollection;
use Tests\TestCase;

/**
 * @covers \App\Domain\Shared\Traits\HasValueCollection::newCollection
 */
class HasValueCollectionTest extends TestCase
{
    public function testItFallsBackToDataCollectionForValues(): void
    {
        $collection = TestValue::builder()->count(2)->create();
        $this->assertEquals(DataCollection::class, $collection::class);
    }

    public function testItUsesCustomCollectionForValues(): void
    {
        $collection = TestValueWithCollection::builder()->count(2)->create();
        $this->assertInstanceOf(TestValueWithCollectionCollection::class, $collection);
    }
}
