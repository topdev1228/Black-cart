<?php
declare(strict_types=1);

namespace Tests\Unit\Domain\Shared\Values\Casts;

use App\Domain\Shared\Values\Casts\StringTransform;
use Spatie\LaravelData\Support\DataProperty;
use Spatie\LaravelData\Support\Transformation\TransformationContext;
use Tests\TestCase;

class StringTransformTest extends TestCase
{
    public function testItTransforms(): void
    {
        $stringTransformer = new StringTransform('upper');

        $property = $this->createMock(DataProperty::class);
        $value = 'test';

        $result = $stringTransformer->transform($property, $value, $this->mock(TransformationContext::class));

        $this->assertEquals('TEST', $result);
    }

    public function testItTransformWithMultipleOperations(): void
    {
        $stringTransformer = new StringTransform('lower', 'kebab', 'reverse');

        $property = $this->createMock(DataProperty::class);
        $value = 'Test String';

        $result = $stringTransformer->transform($property, $value, $this->mock(TransformationContext::class));

        $this->assertEquals('gnirts-tset', $result);
    }

    public function testItTransformWithArguments(): void
    {
        $stringTransformer = new StringTransform('lower', 'kebab', 'replace:replace,test');

        $property = $this->createMock(DataProperty::class);
        $value = 'Test Replace';

        $result = $stringTransformer->transform($property, $value, $this->mock(TransformationContext::class));

        $this->assertEquals('test-test', $result);
    }
}
