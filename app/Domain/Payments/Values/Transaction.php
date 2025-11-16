<?php
declare(strict_types=1);

namespace App\Domain\Payments\Values;

use App\Domain\Payments\Enums\TransactionKind;
use App\Domain\Payments\Enums\TransactionStatus;
use App\Domain\Shared\Traits\HasValueCollection;
use App\Domain\Shared\Traits\HasValueFactory;
use App\Domain\Shared\Values\Casts\MoneyValue;
use App\Domain\Shared\Values\Value;
use Brick\Money\Money;
use Carbon\Carbon;
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
        public string $sourceOrderId,
        public TransactionKind $kind,
        public TransactionStatus $status,
        #[WithCast(MoneyValue::class, 'shop_currency', isMinorValue: false)]
        #[WithTransformer(MoneyValue::class, isMinorValue: true)]
        public Money $shopAmount,
        public CurrencyAlpha3 $shopCurrency,
        #[WithCast(MoneyValue::class, 'customer_currency', isMinorValue: false)]
        #[WithTransformer(MoneyValue::class, isMinorValue: true)]
        public Money $customerAmount,
        public CurrencyAlpha3 $customerCurrency,
        public ?string $id = null,
        public ?string $sourceId = null,
        public ?string $transactionSourceName = null,
        public CarbonImmutable|Carbon|null $authorizationExpiresAt = null,
        public ?string $parentTransactionId = null,
        public ?string $parentTransactionSourceId = null,
        public ?string $capturedTransactionId = null,
        public ?string $capturedTransactionSourceId = null,
    ) {
    }
}
