<?php
declare(strict_types=1);

namespace App\Domain\Orders\Repositories;

use App\Domain\Orders\Enums\OrderStatus;
use App\Domain\Orders\Models\Order;
use App\Domain\Orders\Values\Collections\OrderCollection;
use App\Domain\Orders\Values\Order as OrderValue;

// This class is for internal console commands use only
// Don't expose this method via APIs
class InternalOrderRepository
{
    public function getAllOrders(): OrderCollection
    {
        /** @psalm-suppress InvalidArgument */
        return OrderValue::collection(
            Order::withoutCurrentStore()
                ->withoutTrashed()
                ->without(['lineItems', 'refunds', 'returns', 'transactions'])
                ->get()
        );
    }

    public function getInTrialOrdersWithNullTrialExpiryDate(): OrderCollection
    {
        /** @psalm-suppress InvalidArgument */
        return OrderValue::collection(
            Order::withoutCurrentStore()
                ->withoutTrashed()
                ->without(['lineItems', 'refunds', 'returns', 'transactions'])
                ->where('status', OrderStatus::IN_TRIAL)
                ->whereNull('trial_expires_at')
                ->get()
        );
    }

    public function getAllActiveOrders(): OrderCollection
    {
        /** @psalm-suppress InvalidArgument */
        return OrderValue::collection(
            Order::withoutCurrentStore()
                ->withoutTrashed()
                ->without(['lineItems', 'refunds', 'returns', 'transactions'])
                ->whereIn('status', [OrderStatus::IN_TRIAL, OrderStatus::OPEN])
                ->get()
        );
    }
}
