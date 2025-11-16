<?php declare(strict_types=1);

namespace App\Domain\Orders\GraphQL\Shopify\Stable\Types;

/**
 * @property array<string> $delegateAccessScope
 * @property int|null $expiresIn
 */
class DelegateAccessTokenInput extends \Spawnia\Sailor\ObjectLike
{
    /**
     * @param array<string> $delegateAccessScope
     * @param int|null $expiresIn
     */
    public static function make(
        $delegateAccessScope,
        $expiresIn = 'Special default value that allows Sailor to differentiate between explicitly passing null and not passing a value at all.',
    ): self {
        $instance = new self;

        if ($delegateAccessScope !== self::UNDEFINED) {
            $instance->delegateAccessScope = $delegateAccessScope;
        }
        if ($expiresIn !== self::UNDEFINED) {
            $instance->expiresIn = $expiresIn;
        }

        return $instance;
    }

    protected function converters(): array
    {
        static $converters;

        return $converters ??= [
            'delegateAccessScope' => new \Spawnia\Sailor\Convert\NonNullConverter(new \Spawnia\Sailor\Convert\ListConverter(new \Spawnia\Sailor\Convert\NonNullConverter(new \Spawnia\Sailor\Convert\StringConverter))),
            'expiresIn' => new \Spawnia\Sailor\Convert\NullConverter(new \Spawnia\Sailor\Convert\IntConverter),
        ];
    }

    public static function endpoint(): string
    {
        return 'shopify-orders-2024-01';
    }

    public static function config(): string
    {
        return \Safe\realpath(__DIR__ . '/../../../../../../../sailor.php');
    }
}
