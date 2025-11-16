<?php
declare(strict_types=1);

namespace App\Domain\Orders\Values;

use App\Domain\Shared\Traits\HasValueFactory;
use App\Domain\Shared\Values\Casts\MoneyValue;
use App\Domain\Shared\Values\Casts\StringTransform;
use App\Domain\Shared\Values\Value;
use Brick\Money\Money;
use Carbon\CarbonImmutable;
use PrinsFrank\Standards\Currency\CurrencyAlpha3;
use Spatie\LaravelData\Attributes\MapInputName;
use Spatie\LaravelData\Attributes\MapName;
use Spatie\LaravelData\Attributes\WithCast;
use Spatie\LaravelData\Attributes\WithTransformer;
use Spatie\LaravelData\Casts\DateTimeInterfaceCast;
use Spatie\LaravelData\Mappers\SnakeCaseMapper;

#[MapName(SnakeCaseMapper::class)]
class WebhookPaymentSchedulesDue extends Value
{
    use HasValueFactory;

    public function __construct(
        #[MapInputName('admin_graphql_api_id')]
        #[WithCast(StringTransform::class, 'shopifyGid:PaymentSchedule')]
        public string $paymentScheduleSourceId,

        // This is the numeric part of the payment term ID which is how we store it in orders.payment_terms_id
        public string $paymentTermsId,
        #[MapInputName('amount')]
        #[WithCast(MoneyValue::class, currencyAttribute: 'customerCurrency', isMinorValue: false)]
        #[WithTransformer(MoneyValue::class, isMinorValue: true)]
        public Money|int $customerAmount,
        #[MapInputName('currency')]
        public CurrencyAlpha3 $customerCurrency,
        #[WithCast(DateTimeInterfaceCast::class)]
        public CarbonImmutable $dueAt,
        #[WithCast(DateTimeInterfaceCast::class)]
        public CarbonImmutable|null $issuedAt = null,
        #[WithCast(DateTimeInterfaceCast::class)]
        public CarbonImmutable|null $completedAt = null,
    ) {
    }
}
