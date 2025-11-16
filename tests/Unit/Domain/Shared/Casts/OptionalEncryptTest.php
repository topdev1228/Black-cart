<?php
declare(strict_types=1);

namespace Tests\Unit\Domain\Shared\Casts;

use App\Domain\Shared\Models\Casts\OptionalEncrypt;
use App\Domain\Shared\Traits\OptionalSecure;
use Illuminate\Database\Eloquent\Model;
use Tests\TestCase;

class OptionalEncryptTest extends TestCase
{
    public function testItEncrypts(): void
    {
        $model = new class extends Model {
            use OptionalSecure;

            protected $casts = [
                'encrypted' => OptionalEncrypt::class,
                'is_secure' => 'boolean',
            ];
        };

        $model->is_secure = true;
        $model->encrypted = 'foo';
        $this->assertEquals('foo', $model->encrypted);
        $this->assertStringStartsWith('eyJpdiI6', $this->getProtectedAttribute($model, 'attributes')['encrypted']);
    }

    public function testItDoesNotEncrypt(): void
    {
        $model = new class extends Model {
            use OptionalSecure;

            protected $casts = [
                'encrypted' => OptionalEncrypt::class,
                'is_secure' => 'boolean',
            ];
        };

        $model->is_secure = false;
        $model->encrypted = 'foo';
        $this->assertEquals('foo', $model->encrypted);
        $this->assertStringStartsWith('foo', $this->getProtectedAttribute($model, 'attributes')['encrypted']);
    }

    public function testItEncryptsCustomIsSecureColumn(): void
    {
        $model = new class extends Model {
            use OptionalSecure;

            protected $casts = [
                'encrypted' => OptionalEncrypt::class,
                'secure' => 'boolean',
            ];

            public static function secureColumn(): string
            {
                return 'secure';
            }
        };

        $model->secure = true;
        $model->encrypted = 'foo';
        $this->assertEquals('foo', $model->encrypted);
        $this->assertStringStartsWith('eyJpdiI6', $this->getProtectedAttribute($model, 'attributes')['encrypted']);
    }

    public function testItEncryptsMultiple(): void
    {
        $model = new class extends Model {
            use OptionalSecure;

            protected $casts = [
                'encrypted' => OptionalEncrypt::class,
                'encrypted2' => OptionalEncrypt::class,
                'encrypted3' => OptionalEncrypt::class,
                'is_secure' => 'boolean',
            ];
        };

        $model->is_secure = true;
        $model->encrypted = 'foo';
        $model->encrypted2 = 'foo';
        $model->encrypted3 = 'foo';
        $model->plain = 'foo';
        $this->assertEquals('foo', $model->encrypted);
        $this->assertEquals('foo', $model->encrypted2);
        $this->assertEquals('foo', $model->encrypted3);
        $this->assertEquals('foo', $model->plain);
        $this->assertStringStartsWith('eyJpdiI6', $this->getProtectedAttribute($model, 'attributes')['encrypted']);
        $this->assertStringStartsWith('eyJpdiI6', $this->getProtectedAttribute($model, 'attributes')['encrypted2']);
        $this->assertStringStartsWith('eyJpdiI6', $this->getProtectedAttribute($model, 'attributes')['encrypted3']);
        $this->assertStringStartsWith('foo', $this->getProtectedAttribute($model, 'attributes')['plain']);
    }
}
