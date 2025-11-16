<?php
declare(strict_types=1);

namespace Tests\Unit\Domain\Shared\Traits;

use App\Domain\Shared\Traits\HasTableName;
use Illuminate\Database\Eloquent\Model;
use Tests\TestCase;

class HasTableNameTest extends TestCase
{
    public function testItReturnsTheTableName(): void
    {
        $model = new class extends Model {
            use HasTableName;

            protected $table = 'test';
        };

        $this->assertEquals('test', $model::table());
    }

    public function testItReturnsQualifiedColumnName(): void
    {
        $model = new class extends Model {
            use HasTableName;

            protected $table = 'test';
        };

        $this->assertEquals('test.test', $model::col('test'));
    }
}
