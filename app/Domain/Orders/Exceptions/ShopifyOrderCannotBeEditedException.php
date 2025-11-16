<?php
declare(strict_types=1);

namespace App\Domain\Orders\Exceptions;

use App\Domain\Shared\Exceptions\Shopify\ShopifyClientException;
use App\Enums\Exceptions\ApiExceptionErrorCodes;

class ShopifyOrderCannotBeEditedException extends ShopifyClientException
{
    const MESSAGE = 'Shopify orderEditBegin: the order cannot be edited.';

    public function __construct()
    {
        parent::__construct(static::MESSAGE, 409, ApiExceptionErrorCodes::INVALID_REQUEST);
    }
}
