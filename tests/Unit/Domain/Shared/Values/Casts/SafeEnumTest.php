<?php

declare(strict_types=1);

namespace Tests\Unit\Domain\Shared\Values\Casts;

use App\Domain\Billings\Enums\SubscriptionStatus;
use App\Domain\Billings\Values\ShopifyAppSubscription;
use App\Domain\Billings\Values\Subscription;
use App\Domain\Shared\Values\Casts\SafeEnum;
use ReflectionClass;
use ReflectionProperty;
use Spatie\LaravelData\Support\Creation\CreationContext;
use Spatie\LaravelData\Support\Factories\DataPropertyFactory;
use Tests\TestCase;
use ValueError;

class SafeEnumTest extends TestCase
{
    public function testItCastsOriginalValue(): void
    {
        $safeEnum = new SafeEnum('lower');

        $property = resolve(DataPropertyFactory::class)->build(new ReflectionProperty(ShopifyAppSubscription::class, 'status'), new ReflectionClass(ShopifyAppSubscription::class));

        $value = 'active';
        $result = $safeEnum->cast($property, $value, [], $this->mock(CreationContext::class));

        $this->assertEquals(SubscriptionStatus::ACTIVE, $result);
    }

    public function testItCastsTransformedValue(): void
    {
        $safeEnum = new SafeEnum('lower');

        $property = resolve(DataPropertyFactory::class)->build(new ReflectionProperty(ShopifyAppSubscription::class, 'status'), new ReflectionClass(ShopifyAppSubscription::class));

        $value = 'ACTIVE';
        $result = $safeEnum->cast($property, $value, [], $this->mock(CreationContext::class));

        $this->assertEquals(SubscriptionStatus::ACTIVE, $result);
    }

    public function testItThrowsOnUnknown(): void
    {
        $safeEnum = new SafeEnum('lower');

        $property = resolve(DataPropertyFactory::class)->build(new ReflectionProperty(ShopifyAppSubscription::class, 'status'), new ReflectionClass(ShopifyAppSubscription::class));

        $value = 'foo';

        $this->expectException(ValueError::class);
        $this->expectExceptionMessage('"foo" is not a valid backing value for enum "App\Domain\Billings\Enums\SubscriptionStatus"');
        $result = $safeEnum->cast($property, $value, [], $this->mock(CreationContext::class));
    }

    public function testItFallsBackToDefault(): void
    {
        $safeEnum = new SafeEnum('lower');

        $property = resolve(DataPropertyFactory::class)->build(new ReflectionProperty(Subscription::class, 'status'), new ReflectionClass(Subscription::class), true, SubscriptionStatus::ACTIVE);

        $value = 'foo';

        $result = $safeEnum->cast($property, $value, [], $this->mock(CreationContext::class));

        $this->assertEquals(SubscriptionStatus::ACTIVE, $result);
    }
}
