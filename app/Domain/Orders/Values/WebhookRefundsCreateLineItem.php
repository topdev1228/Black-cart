<?php

declare(strict_types=1);

namespace App\Domain\Orders\Values;

use App\Domain\Shared\Traits\HasValueFactory;
use App\Domain\Shared\Values\ShopifyMoneySetWithCurrencyCode;
use App\Domain\Shared\Values\Value;
use Spatie\LaravelData\Attributes\DataCollectionOf;
use Spatie\LaravelData\Attributes\MapName;
use Spatie\LaravelData\DataCollection;
use Spatie\LaravelData\Mappers\SnakeCaseMapper;

#[MapName(SnakeCaseMapper::class)]
class WebhookRefundsCreateLineItem extends Value
{
    use HasValueFactory;

    public function __construct(
        public string $id,
        public string $title,
        public string $price,
        public string $totalDiscount,
        public ShopifyMoneySetWithCurrencyCode $priceSet,
        public ShopifyMoneySetWithCurrencyCode $totalDiscountSet,
        #[DataCollectionOf(WebhookRefundsCreateLineItemDiscountAllocation::class)]
        public DataCollection $discountAllocations,
        #[DataCollectionOf(WebhookRefundsCreateLineItemTaxLine::class)]
        public DataCollection $taxLines,
        public ?string $name = null,
        public ?string $variantId = null,
        public int $quantity = 1,
        public ?string $sku = null,
        public ?string $vendor = null,
        public string $fulfillmentService = 'manual',
        public ?string $productId = null,
        public bool $requiresShipping = false,
        public bool $taxable = false,
        public bool $giftCard = false,
        public ?string $variantInventoryManagement = null,
        public array $properties = [],
        public bool $productExists = false,
        public int $fulfillableQuantity = 0,
        public int $grams = 0,
        public ?string $variantTitle = null,
        public ?string $fulfillmentStatus = null,
    ) {
    }
}
