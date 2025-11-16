<?php
declare(strict_types=1);

namespace App\Domain\Orders\Enums;

/**
 * @see https://shopify.dev/docs/api/admin-graphql/2023-10/enums/appsubscriptionstatus
 */
enum SubscriptionStatus: string
{
    case ACTIVE = 'active';
    case CANCELLED = 'cancelled';
    case DECLINED = 'declined';
    case EXPIRED = 'expired';
    case FROZEN = 'frozen';
    case PENDING = 'pending';
}
