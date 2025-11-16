<?php
declare(strict_types=1);

namespace App\Domain\Shared\Values\Casts;

use Brick\Money\Money;
use PrinsFrank\Standards\Currency\CurrencyAlpha3;
use Spatie\LaravelData\Casts\Cast;
use Spatie\LaravelData\Support\Creation\CreationContext;
use Spatie\LaravelData\Support\DataProperty;
use Spatie\LaravelData\Support\Transformation\TransformationContext;
use Spatie\LaravelData\Transformers\Transformer;

class MoneyValue implements Cast, Transformer
{
    public function __construct(
        protected string $currencyAttribute = 'currency',
        protected bool $isMinorValue = true,
    ) {
    }

    public function cast(DataProperty $property, mixed $value, array $properties, CreationContext $context): mixed
    {
        $currency = $properties[$this->currencyAttribute];
        if ($currency instanceof CurrencyAlpha3) {
            $currency = $currency->value;
        }

        if ($this->isMinorValue) {
            return Money::ofMinor($value, $currency);
        }

        return Money::of($value, $currency);
    }

    public function transform(DataProperty $property, mixed $value, TransformationContext $context): mixed
    {
        return $this->isMinorValue ? $value->getMinorAmount()->toInt() : $value->getAmount()->toInt();
    }
}
