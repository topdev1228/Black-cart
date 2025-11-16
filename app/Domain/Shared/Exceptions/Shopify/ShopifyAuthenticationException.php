<?php
declare(strict_types=1);

namespace App\Domain\Shared\Exceptions\Shopify;

use App\Exceptions\AuthenticationException;
use Throwable;

class ShopifyAuthenticationException extends AuthenticationException
{
    public function __construct(string $message, Throwable $previous = null)
    {
        parent::__construct('Shopify ' . $message, $previous);
    }
}
