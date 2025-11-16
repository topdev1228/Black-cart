<?php
declare(strict_types=1);

namespace App\Domain\Payments\Exceptions;

use App\Exceptions\ServerApiException;

class ShopifyTransactionNotFoundException extends ServerApiException
{
}
