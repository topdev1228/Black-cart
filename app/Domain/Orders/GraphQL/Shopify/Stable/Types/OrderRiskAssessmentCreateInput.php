<?php declare(strict_types=1);

namespace App\Domain\Orders\GraphQL\Shopify\Stable\Types;

/**
 * @property array<\App\Domain\Orders\GraphQL\Shopify\Stable\Types\OrderRiskAssessmentFactInput> $facts
 * @property int|string $orderId
 * @property string $riskLevel
 */
class OrderRiskAssessmentCreateInput extends \Spawnia\Sailor\ObjectLike
{
    /**
     * @param array<\App\Domain\Orders\GraphQL\Shopify\Stable\Types\OrderRiskAssessmentFactInput> $facts
     * @param int|string $orderId
     * @param string $riskLevel
     */
    public static function make($facts, $orderId, $riskLevel): self
    {
        $instance = new self;

        if ($facts !== self::UNDEFINED) {
            $instance->facts = $facts;
        }
        if ($orderId !== self::UNDEFINED) {
            $instance->orderId = $orderId;
        }
        if ($riskLevel !== self::UNDEFINED) {
            $instance->riskLevel = $riskLevel;
        }

        return $instance;
    }

    protected function converters(): array
    {
        static $converters;

        return $converters ??= [
            'facts' => new \Spawnia\Sailor\Convert\NonNullConverter(new \Spawnia\Sailor\Convert\ListConverter(new \Spawnia\Sailor\Convert\NonNullConverter(new \App\Domain\Orders\GraphQL\Shopify\Stable\Types\OrderRiskAssessmentFactInput))),
            'orderId' => new \Spawnia\Sailor\Convert\NonNullConverter(new \Spawnia\Sailor\Convert\IDConverter),
            'riskLevel' => new \Spawnia\Sailor\Convert\NonNullConverter(new \Spawnia\Sailor\Convert\EnumConverter),
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
