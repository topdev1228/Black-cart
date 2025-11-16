<?php
declare(strict_types=1);

namespace Tests\Unit\Domain\Shared\Casts;

use App\Domain\Shared\Models\Casts\Money as MoneyCast;
use Brick\Money\Exception\MoneyMismatchException;
use Brick\Money\Exception\UnknownCurrencyException;
use Brick\Money\Money;
use Illuminate\Database\Eloquent\Model;
use PrinsFrank\Standards\Currency\CurrencyAlpha3;
use Tests\Fixtures\Domains\Shared\Models\WithMoneyCast;
use Tests\TestCase;

class MoneyTest extends TestCase
{
    public function testItConvertsToMoney(): void
    {
        $model = new class extends Model {
            protected $casts = [
                'amount' => MoneyCast::class,
                'current' => CurrencyAlpha3::class,
            ];
        };

        $model->currency = CurrencyAlpha3::US_Dollar;
        $model->amount = 100;
        $this->assertInstanceOf(Money::class, $model->amount);
        $this->assertEquals(100, $model->amount->getMinorAmount()->toInt());
        $this->assertEquals('USD', $model->amount->getCurrency()->getCurrencyCode());
    }

    public function testItDoesNotConvertNull(): void
    {
        $model = new class extends Model {
            protected $casts = [
                'amount' => MoneyCast::class,
                'current' => CurrencyAlpha3::class,
            ];
        };

        $model->currency = CurrencyAlpha3::US_Dollar;
        $model->amount = null;
        $this->assertNull($model->amount);
    }

    public function testItAllowsFixedCurrency(): void
    {
        $model = new class extends Model {
            protected $casts = [
                'amount' => MoneyCast::class . ':EUR',
            ];
        };

        $model->amount = 100;
        $this->assertInstanceOf(Money::class, $model->amount);
        $this->assertEquals(100, $model->amount->getMinorAmount()->toInt());
        $this->assertEquals('EUR', $model->amount->getCurrency()->getCurrencyCode());
    }

    public function testItAllowsFixedCurrencyOnMondel(): void
    {
        $model = new class extends Model {
            protected $casts = [
                'amount' => MoneyCast::class,
            ];

            public string $currency = 'EUR';
        };

        $model->amount = 100;
        $this->assertInstanceOf(Money::class, $model->amount);
        $this->assertEquals(100, $model->amount->getMinorAmount()->toInt());
        $this->assertEquals('EUR', $model->amount->getCurrency()->getCurrencyCode());
    }

    public function testItThrowsOnUnknownCurrency(): void
    {
        $model = new class extends Model {
            protected $casts = [
                'amount' => MoneyCast::class . ':AAA',
            ];
        };

        $this->expectException(UnknownCurrencyException::class);
        $this->expectExceptionMessage('Unknown currency code: AAA');

        $model->amount = 100;
        $this->assertNull($model->amount);
    }

    public function testItThrowsOnInvalidCurrency(): void
    {
        $model = new class extends Model {
            protected $casts = [
                'amount' => MoneyCast::class . ':AAAA',
            ];
        };

        $this->expectException(UnknownCurrencyException::class);
        $this->expectExceptionMessage('Unknown currency code: AAA');

        $model->amount = 100;
        $this->assertNull($model->amount);
    }

    public function testUseCustomCurrencyColumn(): void
    {
        $model = new class extends Model {
            protected $casts = [
                'amount' => MoneyCast::class . ':currencyCode',
            ];

            public string $currencyCode = 'EUR';
        };

        $model->amount = 100;
        $this->assertInstanceOf(Money::class, $model->amount);
        $this->assertEquals(100, $model->amount->getMinorAmount()->toInt());
        $this->assertEquals('EUR', $model->amount->getCurrency()->getCurrencyCode());
    }

    public function testCastToMoney(): void
    {
        $money = MoneyCast::cast(100, CurrencyAlpha3::US_Dollar->value);

        $this->assertEquals(100, $money->getMinorAmount()->toInt());
        $this->assertEquals('USD', $money->getCurrency()->getCurrencyCode());
    }

    public function testCastToZeroMoney(): void
    {
        $money = MoneyCast::cast(0, CurrencyAlpha3::US_Dollar->value);

        $this->assertEquals(0, $money->getMinorAmount()->toInt());
        $this->assertEquals('USD', $money->getCurrency()->getCurrencyCode());
    }

    public function testItDoesNotOverrideExplicitlySetCurrency(): void
    {
        $model = WithMoneyCast::make([
            'currency' => CurrencyAlpha3::New_Zealand_Dollar,
            'amount' => 100,
        ]);
        $this->assertEquals(CurrencyAlpha3::New_Zealand_Dollar, $model->currency);
        $this->assertEquals('NZ$1.00', $model->amount->formatTo('en_US'));

        $model = WithMoneyCast::make([
            'amount' => 100,
            'currency' => CurrencyAlpha3::New_Zealand_Dollar,
        ]);
        $this->assertEquals(CurrencyAlpha3::New_Zealand_Dollar, $model->currency);
        $this->assertEquals('NZ$1.00', $model->amount->formatTo('en_US'));

        $model = WithMoneyCast::make([
            'currency' => CurrencyAlpha3::New_Zealand_Dollar,
            'amount' => Money::of(1, CurrencyAlpha3::New_Zealand_Dollar->value),
        ]);
        $this->assertEquals(CurrencyAlpha3::New_Zealand_Dollar, $model->currency);
        $this->assertEquals('NZ$1.00', $model->amount->formatTo('en_US'));

        $model = WithMoneyCast::make([
            'amount' => Money::of(1, CurrencyAlpha3::New_Zealand_Dollar->value),
            'currency' => CurrencyAlpha3::New_Zealand_Dollar,
        ]);
        $this->assertEquals(CurrencyAlpha3::New_Zealand_Dollar, $model->currency);
        $this->assertEquals('NZ$1.00', $model->amount->formatTo('en_US'));
    }

    public function testItErrorsWithCurrencyMismatch(): void
    {
        $this->expectException(MoneyMismatchException::class);
        $this->expectExceptionMessage('The monies do not share the same currency: expected AUD, got NZD.');
        WithMoneyCast::make([
            'currency' => CurrencyAlpha3::Australian_Dollar,
            'amount' => Money::of(1, CurrencyAlpha3::New_Zealand_Dollar->value),
        ]);
    }

    public function testItErrorsWithMultipleMoniesOfDifferentCurrencies(): void
    {
        $this->expectException(MoneyMismatchException::class);
        $this->expectExceptionMessage('The monies do not share the same currency: expected AUD, got NZD.');
        WithMoneyCast::make([
            'currency' => CurrencyAlpha3::Australian_Dollar,
            'amount' => Money::of(1, CurrencyAlpha3::Australian_Dollar->value),
            'amount2' => Money::of(2, CurrencyAlpha3::New_Zealand_Dollar->value),
        ]);
    }

    public function testItDoesNotSetCurrencyFromMoney(): void
    {
        $model = WithMoneyCast::make([
            'amount' => Money::of(1, CurrencyAlpha3::New_Zealand_Dollar->value),
        ]);

        $this->assertEquals('NZ$1.00', $model->amount->formatTo('en_US'));
        $this->assertNull($model->currency);
    }
}
