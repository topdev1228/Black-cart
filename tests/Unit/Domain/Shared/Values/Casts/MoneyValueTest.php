<?php
declare(strict_types=1);

namespace Tests\Unit\Domain\Shared\Values\Casts;

use Brick\Money\Money;
use PrinsFrank\Standards\Currency\CurrencyAlpha3;
use Tests\Fixtures\Values\TestValueWithMoney;
use Tests\Fixtures\Values\TestValueWithMoneyNoDefaults;
use Tests\TestCase;

class MoneyValueTest extends TestCase
{
    public function testItCastsToMoney(): void
    {
        $value = TestValueWithMoney::from(['amount' => 100, 'currency' => CurrencyAlpha3::Euro]);

        $this->assertInstanceOf(Money::class, $value->amount);
        $this->assertEquals(100, $value->amount->getMinorAmount()->toInt());
        $this->assertEquals('EUR', $value->amount->getCurrency()->getCurrencyCode());
    }

    public function testItTransformsFromMoney(): void
    {
        $value = TestValueWithMoney::from(['amount' => 100, 'currency' => CurrencyAlpha3::Euro]);

        $this->assertEquals(100, $value->toArray()['amount']);
    }

    public function testItUsesNonDefaults(): void
    {
        $value = TestValueWithMoneyNoDefaults::from(['amount' => 100, 'cur' => CurrencyAlpha3::Euro]);

        $this->assertInstanceOf(Money::class, $value->amount);
        $this->assertEquals(100, $value->amount->getAmount()->toInt());
        $this->assertEquals('EUR', $value->amount->getCurrency()->getCurrencyCode());
        $this->assertEquals(100, $value->toArray()['amount']);
    }
}
