<?php
declare(strict_types=1);

namespace App\Domain\Shopify\Values;

use App\Domain\Shared\Values\Value;
use Spatie\LaravelData\Attributes\MapName;

class JwtPayload extends Value
{
    public function __construct(
        #[MapName('iss')]
        public ?string $issuer = null,
        #[MapName('aud')]
        public ?string $clientId = null,
        #[MapName('sub')]
        public ?string $userId = null,
        #[MapName('exp')]
        public ?string $expiresAt = null,
        #[MapName('nbf')]
        public ?string $activatedAt = null,
        #[MapName('iat')]
        public ?string $issuedAt = null,
        #[MapName('jti')]
        public ?string $tokenId = null,
        #[MapName('sid')]
        public ?string $sessionId = null,
        #[MapName('dest')]
        public ?string $domain = null,
    ) {
    }
}
