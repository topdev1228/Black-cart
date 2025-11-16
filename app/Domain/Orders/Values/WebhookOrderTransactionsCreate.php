<?php
declare(strict_types=1);

namespace App\Domain\Orders\Values;

use App\Domain\Orders\Enums\TransactionKind;
use App\Domain\Orders\Enums\TransactionStatus;
use App\Domain\Shared\Traits\HasValueFactory;
use App\Domain\Shared\Values\Casts\MoneyValue;
use App\Domain\Shared\Values\ShopifyMoneySetWithCurrency;
use App\Domain\Shared\Values\Value;
use Brick\Money\Money;
use Carbon\CarbonImmutable;
use PrinsFrank\Standards\Currency\CurrencyAlpha3;
use Spatie\LaravelData\Attributes\MapName;
use Spatie\LaravelData\Attributes\WithCast;
use Spatie\LaravelData\Attributes\WithTransformer;
use Spatie\LaravelData\Mappers\SnakeCaseMapper;

#[MapName(SnakeCaseMapper::class)]
class WebhookOrderTransactionsCreate extends Value
{
    use HasValueFactory;

    public function __construct(
        public int $id,
        public string $adminGraphqlApiId,
        public int $orderId,
        public TransactionKind $kind,
        public string $gateway,
        public TransactionStatus $status,
        public CarbonImmutable $createdAt,
        public bool $test,
        public CurrencyAlpha3 $currency,
        #[WithCast(MoneyValue::class, 'currency', false)]
        #[WithTransformer(MoneyValue::class, isMinorValue: true)]
        public Money $amount,
        public string $paymentId,
        public bool $manualPaymentGateway,
        public ShopifyMoneySetWithCurrency $totalUnsettledSet,
        public ?string $message = null,
        public ?string $parentId = null,
        public ?string $userId = null,
        public ?CarbonImmutable $processedAt = null,
        public ?string $errorCode = null,
        public ?string $sourceName = null,
    ) {
    }
}
