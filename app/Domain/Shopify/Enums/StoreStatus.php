<?php
declare(strict_types=1);

namespace App\Domain\Shopify\Enums;

enum StoreStatus: string
{
    case ONBOARDING = 'onboarding';
    case ACTIVE = 'active';
    case PAUSED = 'paused';
    case SUSPENDED = 'suspended';
    case UNINSTALLED = 'uninstalled';
}
