<?php declare(strict_types=1);

namespace App\Domain\Orders\GraphQL\Shopify\Stable\Types;

/**
 * @property array<\App\Domain\Orders\GraphQL\Shopify\Stable\Types\FulfillmentOrderMergeInputMergeIntent> $mergeIntents
 */
class FulfillmentOrderMergeInput extends \Spawnia\Sailor\ObjectLike
{
    /**
     * @param array<\App\Domain\Orders\GraphQL\Shopify\Stable\Types\FulfillmentOrderMergeInputMergeIntent> $mergeIntents
     */
    public static function make($mergeIntents): self
    {
        $instance = new self;

        if ($mergeIntents !== self::UNDEFINED) {
            $instance->mergeIntents = $mergeIntents;
        }

        return $instance;
    }

    protected function converters(): array
    {
        static $converters;

        return $converters ??= [
            'mergeIntents' => new \Spawnia\Sailor\Convert\NonNullConverter(new \Spawnia\Sailor\Convert\ListConverter(new \Spawnia\Sailor\Convert\NonNullConverter(new \App\Domain\Orders\GraphQL\Shopify\Stable\Types\FulfillmentOrderMergeInputMergeIntent))),
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
