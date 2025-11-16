<?php
declare(strict_types=1);

namespace App\Domain\Payments\Values;

use App\Domain\Shared\Values\Casts\MoneyValue;
use App\Domain\Shared\Values\Value;
use Brick\Money\Money;
use PrinsFrank\Standards\Currency\CurrencyAlpha3;
use Spatie\LaravelData\Attributes\WithCast;

/**
 * @see \App\Domain\Orders\Events\PaymentRequiredEvent
 */
class PaymentRequiredEvent extends Value
{
    public function __construct(
        public string $orderId,
        public string $sourceOrderId,
        public string $trialGroupId,
        #[WithCast(MoneyValue::class)]
        public Money $amount,
        public CurrencyAlpha3 $currency,
    ) {
    }
}
