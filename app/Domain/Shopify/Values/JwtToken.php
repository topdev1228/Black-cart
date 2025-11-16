<?php
declare(strict_types=1);

namespace App\Domain\Shopify\Values;

use App\Domain\Shared\Values\Value;

class JwtToken extends Value
{
    public function __construct(
        public string $token = '',
    ) {
    }
}
