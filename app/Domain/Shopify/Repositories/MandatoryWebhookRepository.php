<?php
declare(strict_types=1);

namespace App\Domain\Shopify\Repositories;

use App\Domain\Shopify\Models\MandatoryWebhook;
use App\Domain\Shopify\Values\MandatoryWebhook as MandatoryWebhookValue;

class MandatoryWebhookRepository
{
    public function store(MandatoryWebhookValue $mandatoryWebhookValue): MandatoryWebhookValue
    {
        return MandatoryWebhookValue::from(MandatoryWebhook::create($mandatoryWebhookValue->toArray()));
    }
}
