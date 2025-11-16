<?php declare(strict_types=1);

namespace App\Domain\Orders\GraphQL\Shopify\Stable\Types;

/**
 * @property \App\Domain\Orders\GraphQL\Shopify\Stable\Types\ProductClaimOwnershipInput|null $claimOwnership
 * @property array<int|string>|null $collectionsToJoin
 * @property array<int|string>|null $collectionsToLeave
 * @property string|null $combinedListingRole
 * @property string|null $customProductType
 * @property string|null $descriptionHtml
 * @property bool|null $giftCard
 * @property string|null $giftCardTemplateSuffix
 * @property string|null $handle
 * @property int|string|null $id
 * @property array<\App\Domain\Orders\GraphQL\Shopify\Stable\Types\MetafieldInput>|null $metafields
 * @property \App\Domain\Orders\GraphQL\Shopify\Stable\Types\ProductCategoryInput|null $productCategory
 * @property array<\App\Domain\Orders\GraphQL\Shopify\Stable\Types\OptionCreateInput>|null $productOptions
 * @property string|null $productType
 * @property bool|null $redirectNewHandle
 * @property bool|null $requiresSellingPlan
 * @property \App\Domain\Orders\GraphQL\Shopify\Stable\Types\SEOInput|null $seo
 * @property \App\Domain\Orders\GraphQL\Shopify\Stable\Types\StandardizedProductTypeInput|null $standardizedProductType
 * @property string|null $status
 * @property array<string>|null $tags
 * @property string|null $templateSuffix
 * @property string|null $title
 * @property string|null $vendor
 */
class ProductInput extends \Spawnia\Sailor\ObjectLike
{
    /**
     * @param \App\Domain\Orders\GraphQL\Shopify\Stable\Types\ProductClaimOwnershipInput|null $claimOwnership
     * @param array<int|string>|null $collectionsToJoin
     * @param array<int|string>|null $collectionsToLeave
     * @param string|null $combinedListingRole
     * @param string|null $customProductType
     * @param string|null $descriptionHtml
     * @param bool|null $giftCard
     * @param string|null $giftCardTemplateSuffix
     * @param string|null $handle
     * @param int|string|null $id
     * @param array<\App\Domain\Orders\GraphQL\Shopify\Stable\Types\MetafieldInput>|null $metafields
     * @param \App\Domain\Orders\GraphQL\Shopify\Stable\Types\ProductCategoryInput|null $productCategory
     * @param array<\App\Domain\Orders\GraphQL\Shopify\Stable\Types\OptionCreateInput>|null $productOptions
     * @param string|null $productType
     * @param bool|null $redirectNewHandle
     * @param bool|null $requiresSellingPlan
     * @param \App\Domain\Orders\GraphQL\Shopify\Stable\Types\SEOInput|null $seo
     * @param \App\Domain\Orders\GraphQL\Shopify\Stable\Types\StandardizedProductTypeInput|null $standardizedProductType
     * @param string|null $status
     * @param array<string>|null $tags
     * @param string|null $templateSuffix
     * @param string|null $title
     * @param string|null $vendor
     */
    public static function make(
        $claimOwnership = 'Special default value that allows Sailor to differentiate between explicitly passing null and not passing a value at all.',
        $collectionsToJoin = 'Special default value that allows Sailor to differentiate between explicitly passing null and not passing a value at all.',
        $collectionsToLeave = 'Special default value that allows Sailor to differentiate between explicitly passing null and not passing a value at all.',
        $combinedListingRole = 'Special default value that allows Sailor to differentiate between explicitly passing null and not passing a value at all.',
        $customProductType = 'Special default value that allows Sailor to differentiate between explicitly passing null and not passing a value at all.',
        $descriptionHtml = 'Special default value that allows Sailor to differentiate between explicitly passing null and not passing a value at all.',
        $giftCard = 'Special default value that allows Sailor to differentiate between explicitly passing null and not passing a value at all.',
        $giftCardTemplateSuffix = 'Special default value that allows Sailor to differentiate between explicitly passing null and not passing a value at all.',
        $handle = 'Special default value that allows Sailor to differentiate between explicitly passing null and not passing a value at all.',
        $id = 'Special default value that allows Sailor to differentiate between explicitly passing null and not passing a value at all.',
        $metafields = 'Special default value that allows Sailor to differentiate between explicitly passing null and not passing a value at all.',
        $productCategory = 'Special default value that allows Sailor to differentiate between explicitly passing null and not passing a value at all.',
        $productOptions = 'Special default value that allows Sailor to differentiate between explicitly passing null and not passing a value at all.',
        $productType = 'Special default value that allows Sailor to differentiate between explicitly passing null and not passing a value at all.',
        $redirectNewHandle = 'Special default value that allows Sailor to differentiate between explicitly passing null and not passing a value at all.',
        $requiresSellingPlan = 'Special default value that allows Sailor to differentiate between explicitly passing null and not passing a value at all.',
        $seo = 'Special default value that allows Sailor to differentiate between explicitly passing null and not passing a value at all.',
        $standardizedProductType = 'Special default value that allows Sailor to differentiate between explicitly passing null and not passing a value at all.',
        $status = 'Special default value that allows Sailor to differentiate between explicitly passing null and not passing a value at all.',
        $tags = 'Special default value that allows Sailor to differentiate between explicitly passing null and not passing a value at all.',
        $templateSuffix = 'Special default value that allows Sailor to differentiate between explicitly passing null and not passing a value at all.',
        $title = 'Special default value that allows Sailor to differentiate between explicitly passing null and not passing a value at all.',
        $vendor = 'Special default value that allows Sailor to differentiate between explicitly passing null and not passing a value at all.',
    ): self {
        $instance = new self;

        if ($claimOwnership !== self::UNDEFINED) {
            $instance->claimOwnership = $claimOwnership;
        }
        if ($collectionsToJoin !== self::UNDEFINED) {
            $instance->collectionsToJoin = $collectionsToJoin;
        }
        if ($collectionsToLeave !== self::UNDEFINED) {
            $instance->collectionsToLeave = $collectionsToLeave;
        }
        if ($combinedListingRole !== self::UNDEFINED) {
            $instance->combinedListingRole = $combinedListingRole;
        }
        if ($customProductType !== self::UNDEFINED) {
            $instance->customProductType = $customProductType;
        }
        if ($descriptionHtml !== self::UNDEFINED) {
            $instance->descriptionHtml = $descriptionHtml;
        }
        if ($giftCard !== self::UNDEFINED) {
            $instance->giftCard = $giftCard;
        }
        if ($giftCardTemplateSuffix !== self::UNDEFINED) {
            $instance->giftCardTemplateSuffix = $giftCardTemplateSuffix;
        }
        if ($handle !== self::UNDEFINED) {
            $instance->handle = $handle;
        }
        if ($id !== self::UNDEFINED) {
            $instance->id = $id;
        }
        if ($metafields !== self::UNDEFINED) {
            $instance->metafields = $metafields;
        }
        if ($productCategory !== self::UNDEFINED) {
            $instance->productCategory = $productCategory;
        }
        if ($productOptions !== self::UNDEFINED) {
            $instance->productOptions = $productOptions;
        }
        if ($productType !== self::UNDEFINED) {
            $instance->productType = $productType;
        }
        if ($redirectNewHandle !== self::UNDEFINED) {
            $instance->redirectNewHandle = $redirectNewHandle;
        }
        if ($requiresSellingPlan !== self::UNDEFINED) {
            $instance->requiresSellingPlan = $requiresSellingPlan;
        }
        if ($seo !== self::UNDEFINED) {
            $instance->seo = $seo;
        }
        if ($standardizedProductType !== self::UNDEFINED) {
            $instance->standardizedProductType = $standardizedProductType;
        }
        if ($status !== self::UNDEFINED) {
            $instance->status = $status;
        }
        if ($tags !== self::UNDEFINED) {
            $instance->tags = $tags;
        }
        if ($templateSuffix !== self::UNDEFINED) {
            $instance->templateSuffix = $templateSuffix;
        }
        if ($title !== self::UNDEFINED) {
            $instance->title = $title;
        }
        if ($vendor !== self::UNDEFINED) {
            $instance->vendor = $vendor;
        }

        return $instance;
    }

    protected function converters(): array
    {
        static $converters;

        return $converters ??= [
            'claimOwnership' => new \Spawnia\Sailor\Convert\NullConverter(new \App\Domain\Orders\GraphQL\Shopify\Stable\Types\ProductClaimOwnershipInput),
            'collectionsToJoin' => new \Spawnia\Sailor\Convert\NullConverter(new \Spawnia\Sailor\Convert\ListConverter(new \Spawnia\Sailor\Convert\NonNullConverter(new \Spawnia\Sailor\Convert\IDConverter))),
            'collectionsToLeave' => new \Spawnia\Sailor\Convert\NullConverter(new \Spawnia\Sailor\Convert\ListConverter(new \Spawnia\Sailor\Convert\NonNullConverter(new \Spawnia\Sailor\Convert\IDConverter))),
            'combinedListingRole' => new \Spawnia\Sailor\Convert\NullConverter(new \Spawnia\Sailor\Convert\EnumConverter),
            'customProductType' => new \Spawnia\Sailor\Convert\NullConverter(new \Spawnia\Sailor\Convert\StringConverter),
            'descriptionHtml' => new \Spawnia\Sailor\Convert\NullConverter(new \Spawnia\Sailor\Convert\StringConverter),
            'giftCard' => new \Spawnia\Sailor\Convert\NullConverter(new \Spawnia\Sailor\Convert\BooleanConverter),
            'giftCardTemplateSuffix' => new \Spawnia\Sailor\Convert\NullConverter(new \Spawnia\Sailor\Convert\StringConverter),
            'handle' => new \Spawnia\Sailor\Convert\NullConverter(new \Spawnia\Sailor\Convert\StringConverter),
            'id' => new \Spawnia\Sailor\Convert\NullConverter(new \Spawnia\Sailor\Convert\IDConverter),
            'metafields' => new \Spawnia\Sailor\Convert\NullConverter(new \Spawnia\Sailor\Convert\ListConverter(new \Spawnia\Sailor\Convert\NonNullConverter(new \App\Domain\Orders\GraphQL\Shopify\Stable\Types\MetafieldInput))),
            'productCategory' => new \Spawnia\Sailor\Convert\NullConverter(new \App\Domain\Orders\GraphQL\Shopify\Stable\Types\ProductCategoryInput),
            'productOptions' => new \Spawnia\Sailor\Convert\NullConverter(new \Spawnia\Sailor\Convert\ListConverter(new \Spawnia\Sailor\Convert\NonNullConverter(new \App\Domain\Orders\GraphQL\Shopify\Stable\Types\OptionCreateInput))),
            'productType' => new \Spawnia\Sailor\Convert\NullConverter(new \Spawnia\Sailor\Convert\StringConverter),
            'redirectNewHandle' => new \Spawnia\Sailor\Convert\NullConverter(new \Spawnia\Sailor\Convert\BooleanConverter),
            'requiresSellingPlan' => new \Spawnia\Sailor\Convert\NullConverter(new \Spawnia\Sailor\Convert\BooleanConverter),
            'seo' => new \Spawnia\Sailor\Convert\NullConverter(new \App\Domain\Orders\GraphQL\Shopify\Stable\Types\SEOInput),
            'standardizedProductType' => new \Spawnia\Sailor\Convert\NullConverter(new \App\Domain\Orders\GraphQL\Shopify\Stable\Types\StandardizedProductTypeInput),
            'status' => new \Spawnia\Sailor\Convert\NullConverter(new \Spawnia\Sailor\Convert\EnumConverter),
            'tags' => new \Spawnia\Sailor\Convert\NullConverter(new \Spawnia\Sailor\Convert\ListConverter(new \Spawnia\Sailor\Convert\NonNullConverter(new \Spawnia\Sailor\Convert\StringConverter))),
            'templateSuffix' => new \Spawnia\Sailor\Convert\NullConverter(new \Spawnia\Sailor\Convert\StringConverter),
            'title' => new \Spawnia\Sailor\Convert\NullConverter(new \Spawnia\Sailor\Convert\StringConverter),
            'vendor' => new \Spawnia\Sailor\Convert\NullConverter(new \Spawnia\Sailor\Convert\StringConverter),
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
