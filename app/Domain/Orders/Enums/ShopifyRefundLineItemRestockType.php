<?php
declare(strict_types=1);

namespace App\Domain\Orders\Enums;

enum ShopifyRefundLineItemRestockType: string
{
    case CANCEL = 'cancel';
    case RETURN = 'return';
    case NO_RESTOCK = 'no-restock';
    /**
     * @deprecated The refund line item was restocked, without specifically being identified as a return or cancelation. This value is not accepted when creating new refunds.
     */
    case LEGACY_RESTOCK = 'legacy-restock';
}
