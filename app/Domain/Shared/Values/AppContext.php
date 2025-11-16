<?php

declare(strict_types=1);

namespace App\Domain\Shared\Values;

use App\Domain\Shared\Traits\HasValueFactory;
use App\Domain\Shared\Traits\Strict;
use App\Domain\Shopify\Values\JwtToken;
use App\Domain\Stores\Values\Store;

class AppContext extends Value
{
    use Strict;
    use HasValueFactory;

    public function __construct(
        public JwtToken $jwtToken,
        public Store $store,
    ) {
    }
}
