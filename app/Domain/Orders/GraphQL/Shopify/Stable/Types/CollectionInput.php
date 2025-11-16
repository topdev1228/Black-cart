<?php declare(strict_types=1);

namespace App\Domain\Orders\GraphQL\Shopify\Stable\Types;

/**
 * @property string|null $descriptionHtml
 * @property string|null $handle
 * @property int|string|null $id
 * @property \App\Domain\Orders\GraphQL\Shopify\Stable\Types\ImageInput|null $image
 * @property array<\App\Domain\Orders\GraphQL\Shopify\Stable\Types\MetafieldInput>|null $metafields
 * @property array<int|string>|null $products
 * @property bool|null $redirectNewHandle
 * @property \App\Domain\Orders\GraphQL\Shopify\Stable\Types\CollectionRuleSetInput|null $ruleSet
 * @property \App\Domain\Orders\GraphQL\Shopify\Stable\Types\SEOInput|null $seo
 * @property string|null $sortOrder
 * @property string|null $templateSuffix
 * @property string|null $title
 */
class CollectionInput extends \Spawnia\Sailor\ObjectLike
{
    /**
     * @param string|null $descriptionHtml
     * @param string|null $handle
     * @param int|string|null $id
     * @param \App\Domain\Orders\GraphQL\Shopify\Stable\Types\ImageInput|null $image
     * @param array<\App\Domain\Orders\GraphQL\Shopify\Stable\Types\MetafieldInput>|null $metafields
     * @param array<int|string>|null $products
     * @param bool|null $redirectNewHandle
     * @param \App\Domain\Orders\GraphQL\Shopify\Stable\Types\CollectionRuleSetInput|null $ruleSet
     * @param \App\Domain\Orders\GraphQL\Shopify\Stable\Types\SEOInput|null $seo
     * @param string|null $sortOrder
     * @param string|null $templateSuffix
     * @param string|null $title
     */
    public static function make(
        $descriptionHtml = 'Special default value that allows Sailor to differentiate between explicitly passing null and not passing a value at all.',
        $handle = 'Special default value that allows Sailor to differentiate between explicitly passing null and not passing a value at all.',
        $id = 'Special default value that allows Sailor to differentiate between explicitly passing null and not passing a value at all.',
        $image = 'Special default value that allows Sailor to differentiate between explicitly passing null and not passing a value at all.',
        $metafields = 'Special default value that allows Sailor to differentiate between explicitly passing null and not passing a value at all.',
        $products = 'Special default value that allows Sailor to differentiate between explicitly passing null and not passing a value at all.',
        $redirectNewHandle = 'Special default value that allows Sailor to differentiate between explicitly passing null and not passing a value at all.',
        $ruleSet = 'Special default value that allows Sailor to differentiate between explicitly passing null and not passing a value at all.',
        $seo = 'Special default value that allows Sailor to differentiate between explicitly passing null and not passing a value at all.',
        $sortOrder = 'Special default value that allows Sailor to differentiate between explicitly passing null and not passing a value at all.',
        $templateSuffix = 'Special default value that allows Sailor to differentiate between explicitly passing null and not passing a value at all.',
        $title = 'Special default value that allows Sailor to differentiate between explicitly passing null and not passing a value at all.',
    ): self {
        $instance = new self;

        if ($descriptionHtml !== self::UNDEFINED) {
            $instance->descriptionHtml = $descriptionHtml;
        }
        if ($handle !== self::UNDEFINED) {
            $instance->handle = $handle;
        }
        if ($id !== self::UNDEFINED) {
            $instance->id = $id;
        }
        if ($image !== self::UNDEFINED) {
            $instance->image = $image;
        }
        if ($metafields !== self::UNDEFINED) {
            $instance->metafields = $metafields;
        }
        if ($products !== self::UNDEFINED) {
            $instance->products = $products;
        }
        if ($redirectNewHandle !== self::UNDEFINED) {
            $instance->redirectNewHandle = $redirectNewHandle;
        }
        if ($ruleSet !== self::UNDEFINED) {
            $instance->ruleSet = $ruleSet;
        }
        if ($seo !== self::UNDEFINED) {
            $instance->seo = $seo;
        }
        if ($sortOrder !== self::UNDEFINED) {
            $instance->sortOrder = $sortOrder;
        }
        if ($templateSuffix !== self::UNDEFINED) {
            $instance->templateSuffix = $templateSuffix;
        }
        if ($title !== self::UNDEFINED) {
            $instance->title = $title;
        }

        return $instance;
    }

    protected function converters(): array
    {
        static $converters;

        return $converters ??= [
            'descriptionHtml' => new \Spawnia\Sailor\Convert\NullConverter(new \Spawnia\Sailor\Convert\StringConverter),
            'handle' => new \Spawnia\Sailor\Convert\NullConverter(new \Spawnia\Sailor\Convert\StringConverter),
            'id' => new \Spawnia\Sailor\Convert\NullConverter(new \Spawnia\Sailor\Convert\IDConverter),
            'image' => new \Spawnia\Sailor\Convert\NullConverter(new \App\Domain\Orders\GraphQL\Shopify\Stable\Types\ImageInput),
            'metafields' => new \Spawnia\Sailor\Convert\NullConverter(new \Spawnia\Sailor\Convert\ListConverter(new \Spawnia\Sailor\Convert\NonNullConverter(new \App\Domain\Orders\GraphQL\Shopify\Stable\Types\MetafieldInput))),
            'products' => new \Spawnia\Sailor\Convert\NullConverter(new \Spawnia\Sailor\Convert\ListConverter(new \Spawnia\Sailor\Convert\NonNullConverter(new \Spawnia\Sailor\Convert\IDConverter))),
            'redirectNewHandle' => new \Spawnia\Sailor\Convert\NullConverter(new \Spawnia\Sailor\Convert\BooleanConverter),
            'ruleSet' => new \Spawnia\Sailor\Convert\NullConverter(new \App\Domain\Orders\GraphQL\Shopify\Stable\Types\CollectionRuleSetInput),
            'seo' => new \Spawnia\Sailor\Convert\NullConverter(new \App\Domain\Orders\GraphQL\Shopify\Stable\Types\SEOInput),
            'sortOrder' => new \Spawnia\Sailor\Convert\NullConverter(new \Spawnia\Sailor\Convert\EnumConverter),
            'templateSuffix' => new \Spawnia\Sailor\Convert\NullConverter(new \Spawnia\Sailor\Convert\StringConverter),
            'title' => new \Spawnia\Sailor\Convert\NullConverter(new \Spawnia\Sailor\Convert\StringConverter),
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
