<?php
declare(strict_types=1);

namespace App\Domain\Shared\Models\Casts;

use function array_key_exists;
use Brick\Money\Currency;
use Brick\Money\Exception\MoneyMismatchException;
use Brick\Money\Exception\UnknownCurrencyException;
use Brick\Money\Money as MoneyValue;
use Illuminate\Contracts\Database\Eloquent\CastsAttributes;
use Illuminate\Database\Eloquent\Model;
use PrinsFrank\Standards\Currency\CurrencyAlpha3;

class Money implements CastsAttributes
{
    public function __construct(protected string $currency = 'currency')
    {
    }

    /**
     * @param array<string, mixed> $attributes
     */
    public function get(Model $model, string $key, mixed $value, array $attributes): ?MoneyValue
    {
        if ($value === null) {
            return null;
        }

        if (array_key_exists($this->currency, $attributes)) {
            $currency = $attributes[$this->currency];
        } elseif (!is_null($model->{$this->currency})) {
            $currency = $model->{$this->currency};
        } elseif (strlen($this->currency) === 3) {
            $currency = Currency::of($this->currency);
        } else {
            throw new UnknownCurrencyException('Unknown currency code: ' . $this->currency);
        }

        if ($currency instanceof CurrencyAlpha3) {
            $currency = $currency->value;
        }

        return MoneyValue::ofMinor($value, $currency, null, config('money.rounding'));
    }

    /**
     * @param array<string, mixed> $attributes
     */
    public function set(Model $model, string $key, mixed $value, array $attributes): array
    {
        if ($value instanceof MoneyValue) {
            $return = [
                $key => $value->getMinorAmount()->toInt(),
            ];

            if (isset($attributes[$this->currency]) && is_string($attributes[$this->currency]) && $attributes[$this->currency] !== $value->getCurrency()->getCurrencyCode()) {
                throw MoneyMismatchException::currencyMismatch(Currency::of($attributes[$this->currency]), $value->getCurrency());
            }

            if (isset($attributes[$this->currency]) && $attributes[$this->currency] instanceof CurrencyAlpha3 && $attributes[$this->currency]->value !== $value->getCurrency()->getCurrencyCode()) {
                throw MoneyMismatchException::currencyMismatch(Currency::of($attributes[$this->currency]->value), $value->getCurrency());
            }

            return $return;
        }

        return [
            $key => $value,
        ];
    }

    /**
     * @param Currency|string $currency
     */
    public static function cast(?int $amount, string $currency): MoneyValue
    {
        if ($amount === null || $amount === 0) {
            return MoneyValue::zero($currency);
        }

        return MoneyValue::ofMinor($amount, $currency, roundingMode: config('money.rounding'));
    }
}
