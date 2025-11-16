<?php
declare(strict_types=1);

namespace Tests\Unit\Domain\Shared\Traits;

use App\Domain\Shared\Traits\HasModelCollection;
use Illuminate\Database\Eloquent\Collection;
use Tests\Fixtures\Models\Collections\TestModelWithCollectionCollection;
use Tests\Fixtures\Models\TestModel;
use Tests\Fixtures\Models\TestModelWithCollection;
use Tests\TestCase;

/**
 * @covers \App\Domain\Shared\Traits\HasValueCollection::newCollection
 */
class HasModelCollectionTest extends TestCase
{
    public function testItFallsBackToCollectionForModels(): void
    {
        $model = new class extends TestModel {
            use HasModelCollection;

            protected $table = 'migrations';
        };

        $collection = $model::all();
        $this->assertEquals(Collection::class, $collection::class);
    }

    public function testItUsesCustomCollectionForModels(): void
    {
        $collection = TestModelWithCollection::all();
        $this->assertInstanceOf(TestModelWithCollectionCollection::class, $collection);
    }
}
