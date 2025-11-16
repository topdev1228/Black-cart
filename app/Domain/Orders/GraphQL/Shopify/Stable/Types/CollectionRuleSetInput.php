<?php declare(strict_types=1);

namespace App\Domain\Orders\GraphQL\Shopify\Stable\Types;

/**
 * @property bool $appliedDisjunctively
 * @property array<\App\Domain\Orders\GraphQL\Shopify\Stable\Types\CollectionRuleInput>|null $rules
 */
class CollectionRuleSetInput extends \Spawnia\Sailor\ObjectLike
{
    /**
     * @param bool $appliedDisjunctively
     * @param array<\App\Domain\Orders\GraphQL\Shopify\Stable\Types\CollectionRuleInput>|null $rules
     */
    public static function make(
        $appliedDisjunctively,
        $rules = 'Special default value that allows Sailor to differentiate between explicitly passing null and not passing a value at all.',
    ): self {
        $instance = new self;

        if ($appliedDisjunctively !== self::UNDEFINED) {
            $instance->appliedDisjunctively = $appliedDisjunctively;
        }
        if ($rules !== self::UNDEFINED) {
            $instance->rules = $rules;
        }

        return $instance;
    }

    protected function converters(): array
    {
        static $converters;

        return $converters ??= [
            'appliedDisjunctively' => new \Spawnia\Sailor\Convert\NonNullConverter(new \Spawnia\Sailor\Convert\BooleanConverter),
            'rules' => new \Spawnia\Sailor\Convert\NullConverter(new \Spawnia\Sailor\Convert\ListConverter(new \Spawnia\Sailor\Convert\NonNullConverter(new \App\Domain\Orders\GraphQL\Shopify\Stable\Types\CollectionRuleInput))),
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
