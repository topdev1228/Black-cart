<?php declare(strict_types=1);

namespace App\Domain\Orders\GraphQL\Shopify\Stable\Types;

/**
 * @property string $description
 * @property string $sentiment
 */
class OrderRiskAssessmentFactInput extends \Spawnia\Sailor\ObjectLike
{
    /**
     * @param string $description
     * @param string $sentiment
     */
    public static function make($description, $sentiment): self
    {
        $instance = new self;

        if ($description !== self::UNDEFINED) {
            $instance->description = $description;
        }
        if ($sentiment !== self::UNDEFINED) {
            $instance->sentiment = $sentiment;
        }

        return $instance;
    }

    protected function converters(): array
    {
        static $converters;

        return $converters ??= [
            'description' => new \Spawnia\Sailor\Convert\NonNullConverter(new \Spawnia\Sailor\Convert\StringConverter),
            'sentiment' => new \Spawnia\Sailor\Convert\NonNullConverter(new \Spawnia\Sailor\Convert\EnumConverter),
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
