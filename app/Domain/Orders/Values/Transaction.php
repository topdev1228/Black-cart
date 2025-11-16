<?php

declare(strict_types=1);

namespace App\Domain\Orders\Values;

use App\Domain\Orders\Enums\TransactionKind;
use App\Domain\Orders\Enums\TransactionStatus;
use App\Domain\Shared\Traits\HasValueCollection;
use App\Domain\Shared\Traits\HasValueFactory;
use App\Domain\Shared\Values\Casts\MoneyValue;
use App\Domain\Shared\Values\Value;
use Brick\Money\Money;
use Carbon\CarbonImmutable;
use PrinsFrank\Standards\Currency\CurrencyAlpha3;
use Spatie\LaravelData\Attributes\MapName;
use Spatie\LaravelData\Attributes\WithCast;
use Spatie\LaravelData\Attributes\WithTransformer;
use Spatie\LaravelData\Mappers\SnakeCaseMapper;

#[MapName(SnakeCaseMapper::class)]
class Transaction extends Value
{
    use HasValueCollection;
    use HasValueFactory;

    public function __construct(
        public string $storeId,
        public string $orderId,
        public string $sourceId,
        public string $sourceOrderId,
        public TransactionKind $kind,
        public string $gateway,
        public string $paymentId,
        public TransactionStatus $status,
        public bool $test = false,
        public array $transactionData = [],
        public CurrencyAlpha3 $shopCurrency = CurrencyAlpha3::US_Dollar,
        public CurrencyAlpha3 $customerCurrency = CurrencyAlpha3::US_Dollar,
        #[WithCast(MoneyValue::class, 'shopCurrency')]
        #[WithTransformer(MoneyValue::class, isMinorValue: true)]
        public Money|int $shopAmount = 0,
        #[WithCast(MoneyValue::class, 'customerCurrency')]
        #[WithTransformer(MoneyValue::class, isMinorValue: true)]
        public Money|int $customerAmount = 0,
        #[WithCast(MoneyValue::class, 'shopCurrency')]
        #[WithTransformer(MoneyValue::class, isMinorValue: true)]
        public Money|int $unsettledShopAmount = 0,
        #[WithCast(MoneyValue::class, 'customerCurrency')]
        #[WithTransformer(MoneyValue::class, isMinorValue: true)]
        public Money|int $unsettledCustomerAmount = 0,
        public ?string $id = null,
        public ?string $orderName = null,
        public ?string $parentTransactionId = null,
        public ?string $parentTransactionSourceId = null,
        public ?string $transactionSourceName = null,
        public ?string $userId = null,
        public ?CarbonImmutable $processedAt = null,
        public ?CarbonImmutable $authorizationExpiresAt = null,
        public ?string $message = null,
        public ?string $errorCode = null,
    ) {
    }
}
