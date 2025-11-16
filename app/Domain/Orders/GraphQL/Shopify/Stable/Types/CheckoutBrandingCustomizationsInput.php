<?php declare(strict_types=1);

namespace App\Domain\Orders\GraphQL\Shopify\Stable\Types;

/**
 * @property \App\Domain\Orders\GraphQL\Shopify\Stable\Types\CheckoutBrandingBuyerJourneyInput|null $buyerJourney
 * @property \App\Domain\Orders\GraphQL\Shopify\Stable\Types\CheckoutBrandingCartLinkInput|null $cartLink
 * @property \App\Domain\Orders\GraphQL\Shopify\Stable\Types\CheckoutBrandingCheckboxInput|null $checkbox
 * @property \App\Domain\Orders\GraphQL\Shopify\Stable\Types\CheckoutBrandingChoiceListInput|null $choiceList
 * @property \App\Domain\Orders\GraphQL\Shopify\Stable\Types\CheckoutBrandingControlInput|null $control
 * @property \App\Domain\Orders\GraphQL\Shopify\Stable\Types\CheckoutBrandingExpressCheckoutInput|null $expressCheckout
 * @property \App\Domain\Orders\GraphQL\Shopify\Stable\Types\CheckoutBrandingImageInput|null $favicon
 * @property \App\Domain\Orders\GraphQL\Shopify\Stable\Types\CheckoutBrandingFooterInput|null $footer
 * @property \App\Domain\Orders\GraphQL\Shopify\Stable\Types\CheckoutBrandingGlobalInput|null $global
 * @property \App\Domain\Orders\GraphQL\Shopify\Stable\Types\CheckoutBrandingHeaderInput|null $header
 * @property \App\Domain\Orders\GraphQL\Shopify\Stable\Types\CheckoutBrandingHeadingLevelInput|null $headingLevel1
 * @property \App\Domain\Orders\GraphQL\Shopify\Stable\Types\CheckoutBrandingHeadingLevelInput|null $headingLevel2
 * @property \App\Domain\Orders\GraphQL\Shopify\Stable\Types\CheckoutBrandingHeadingLevelInput|null $headingLevel3
 * @property \App\Domain\Orders\GraphQL\Shopify\Stable\Types\CheckoutBrandingMainInput|null $main
 * @property \App\Domain\Orders\GraphQL\Shopify\Stable\Types\CheckoutBrandingMerchandiseThumbnailInput|null $merchandiseThumbnail
 * @property \App\Domain\Orders\GraphQL\Shopify\Stable\Types\CheckoutBrandingOrderSummaryInput|null $orderSummary
 * @property \App\Domain\Orders\GraphQL\Shopify\Stable\Types\CheckoutBrandingButtonInput|null $primaryButton
 * @property \App\Domain\Orders\GraphQL\Shopify\Stable\Types\CheckoutBrandingButtonInput|null $secondaryButton
 * @property \App\Domain\Orders\GraphQL\Shopify\Stable\Types\CheckoutBrandingSelectInput|null $select
 * @property \App\Domain\Orders\GraphQL\Shopify\Stable\Types\CheckoutBrandingTextFieldInput|null $textField
 */
class CheckoutBrandingCustomizationsInput extends \Spawnia\Sailor\ObjectLike
{
    /**
     * @param \App\Domain\Orders\GraphQL\Shopify\Stable\Types\CheckoutBrandingBuyerJourneyInput|null $buyerJourney
     * @param \App\Domain\Orders\GraphQL\Shopify\Stable\Types\CheckoutBrandingCartLinkInput|null $cartLink
     * @param \App\Domain\Orders\GraphQL\Shopify\Stable\Types\CheckoutBrandingCheckboxInput|null $checkbox
     * @param \App\Domain\Orders\GraphQL\Shopify\Stable\Types\CheckoutBrandingChoiceListInput|null $choiceList
     * @param \App\Domain\Orders\GraphQL\Shopify\Stable\Types\CheckoutBrandingControlInput|null $control
     * @param \App\Domain\Orders\GraphQL\Shopify\Stable\Types\CheckoutBrandingExpressCheckoutInput|null $expressCheckout
     * @param \App\Domain\Orders\GraphQL\Shopify\Stable\Types\CheckoutBrandingImageInput|null $favicon
     * @param \App\Domain\Orders\GraphQL\Shopify\Stable\Types\CheckoutBrandingFooterInput|null $footer
     * @param \App\Domain\Orders\GraphQL\Shopify\Stable\Types\CheckoutBrandingGlobalInput|null $global
     * @param \App\Domain\Orders\GraphQL\Shopify\Stable\Types\CheckoutBrandingHeaderInput|null $header
     * @param \App\Domain\Orders\GraphQL\Shopify\Stable\Types\CheckoutBrandingHeadingLevelInput|null $headingLevel1
     * @param \App\Domain\Orders\GraphQL\Shopify\Stable\Types\CheckoutBrandingHeadingLevelInput|null $headingLevel2
     * @param \App\Domain\Orders\GraphQL\Shopify\Stable\Types\CheckoutBrandingHeadingLevelInput|null $headingLevel3
     * @param \App\Domain\Orders\GraphQL\Shopify\Stable\Types\CheckoutBrandingMainInput|null $main
     * @param \App\Domain\Orders\GraphQL\Shopify\Stable\Types\CheckoutBrandingMerchandiseThumbnailInput|null $merchandiseThumbnail
     * @param \App\Domain\Orders\GraphQL\Shopify\Stable\Types\CheckoutBrandingOrderSummaryInput|null $orderSummary
     * @param \App\Domain\Orders\GraphQL\Shopify\Stable\Types\CheckoutBrandingButtonInput|null $primaryButton
     * @param \App\Domain\Orders\GraphQL\Shopify\Stable\Types\CheckoutBrandingButtonInput|null $secondaryButton
     * @param \App\Domain\Orders\GraphQL\Shopify\Stable\Types\CheckoutBrandingSelectInput|null $select
     * @param \App\Domain\Orders\GraphQL\Shopify\Stable\Types\CheckoutBrandingTextFieldInput|null $textField
     */
    public static function make(
        $buyerJourney = 'Special default value that allows Sailor to differentiate between explicitly passing null and not passing a value at all.',
        $cartLink = 'Special default value that allows Sailor to differentiate between explicitly passing null and not passing a value at all.',
        $checkbox = 'Special default value that allows Sailor to differentiate between explicitly passing null and not passing a value at all.',
        $choiceList = 'Special default value that allows Sailor to differentiate between explicitly passing null and not passing a value at all.',
        $control = 'Special default value that allows Sailor to differentiate between explicitly passing null and not passing a value at all.',
        $expressCheckout = 'Special default value that allows Sailor to differentiate between explicitly passing null and not passing a value at all.',
        $favicon = 'Special default value that allows Sailor to differentiate between explicitly passing null and not passing a value at all.',
        $footer = 'Special default value that allows Sailor to differentiate between explicitly passing null and not passing a value at all.',
        $global = 'Special default value that allows Sailor to differentiate between explicitly passing null and not passing a value at all.',
        $header = 'Special default value that allows Sailor to differentiate between explicitly passing null and not passing a value at all.',
        $headingLevel1 = 'Special default value that allows Sailor to differentiate between explicitly passing null and not passing a value at all.',
        $headingLevel2 = 'Special default value that allows Sailor to differentiate between explicitly passing null and not passing a value at all.',
        $headingLevel3 = 'Special default value that allows Sailor to differentiate between explicitly passing null and not passing a value at all.',
        $main = 'Special default value that allows Sailor to differentiate between explicitly passing null and not passing a value at all.',
        $merchandiseThumbnail = 'Special default value that allows Sailor to differentiate between explicitly passing null and not passing a value at all.',
        $orderSummary = 'Special default value that allows Sailor to differentiate between explicitly passing null and not passing a value at all.',
        $primaryButton = 'Special default value that allows Sailor to differentiate between explicitly passing null and not passing a value at all.',
        $secondaryButton = 'Special default value that allows Sailor to differentiate between explicitly passing null and not passing a value at all.',
        $select = 'Special default value that allows Sailor to differentiate between explicitly passing null and not passing a value at all.',
        $textField = 'Special default value that allows Sailor to differentiate between explicitly passing null and not passing a value at all.',
    ): self {
        $instance = new self;

        if ($buyerJourney !== self::UNDEFINED) {
            $instance->buyerJourney = $buyerJourney;
        }
        if ($cartLink !== self::UNDEFINED) {
            $instance->cartLink = $cartLink;
        }
        if ($checkbox !== self::UNDEFINED) {
            $instance->checkbox = $checkbox;
        }
        if ($choiceList !== self::UNDEFINED) {
            $instance->choiceList = $choiceList;
        }
        if ($control !== self::UNDEFINED) {
            $instance->control = $control;
        }
        if ($expressCheckout !== self::UNDEFINED) {
            $instance->expressCheckout = $expressCheckout;
        }
        if ($favicon !== self::UNDEFINED) {
            $instance->favicon = $favicon;
        }
        if ($footer !== self::UNDEFINED) {
            $instance->footer = $footer;
        }
        if ($global !== self::UNDEFINED) {
            $instance->global = $global;
        }
        if ($header !== self::UNDEFINED) {
            $instance->header = $header;
        }
        if ($headingLevel1 !== self::UNDEFINED) {
            $instance->headingLevel1 = $headingLevel1;
        }
        if ($headingLevel2 !== self::UNDEFINED) {
            $instance->headingLevel2 = $headingLevel2;
        }
        if ($headingLevel3 !== self::UNDEFINED) {
            $instance->headingLevel3 = $headingLevel3;
        }
        if ($main !== self::UNDEFINED) {
            $instance->main = $main;
        }
        if ($merchandiseThumbnail !== self::UNDEFINED) {
            $instance->merchandiseThumbnail = $merchandiseThumbnail;
        }
        if ($orderSummary !== self::UNDEFINED) {
            $instance->orderSummary = $orderSummary;
        }
        if ($primaryButton !== self::UNDEFINED) {
            $instance->primaryButton = $primaryButton;
        }
        if ($secondaryButton !== self::UNDEFINED) {
            $instance->secondaryButton = $secondaryButton;
        }
        if ($select !== self::UNDEFINED) {
            $instance->select = $select;
        }
        if ($textField !== self::UNDEFINED) {
            $instance->textField = $textField;
        }

        return $instance;
    }

    protected function converters(): array
    {
        static $converters;

        return $converters ??= [
            'buyerJourney' => new \Spawnia\Sailor\Convert\NullConverter(new \App\Domain\Orders\GraphQL\Shopify\Stable\Types\CheckoutBrandingBuyerJourneyInput),
            'cartLink' => new \Spawnia\Sailor\Convert\NullConverter(new \App\Domain\Orders\GraphQL\Shopify\Stable\Types\CheckoutBrandingCartLinkInput),
            'checkbox' => new \Spawnia\Sailor\Convert\NullConverter(new \App\Domain\Orders\GraphQL\Shopify\Stable\Types\CheckoutBrandingCheckboxInput),
            'choiceList' => new \Spawnia\Sailor\Convert\NullConverter(new \App\Domain\Orders\GraphQL\Shopify\Stable\Types\CheckoutBrandingChoiceListInput),
            'control' => new \Spawnia\Sailor\Convert\NullConverter(new \App\Domain\Orders\GraphQL\Shopify\Stable\Types\CheckoutBrandingControlInput),
            'expressCheckout' => new \Spawnia\Sailor\Convert\NullConverter(new \App\Domain\Orders\GraphQL\Shopify\Stable\Types\CheckoutBrandingExpressCheckoutInput),
            'favicon' => new \Spawnia\Sailor\Convert\NullConverter(new \App\Domain\Orders\GraphQL\Shopify\Stable\Types\CheckoutBrandingImageInput),
            'footer' => new \Spawnia\Sailor\Convert\NullConverter(new \App\Domain\Orders\GraphQL\Shopify\Stable\Types\CheckoutBrandingFooterInput),
            'global' => new \Spawnia\Sailor\Convert\NullConverter(new \App\Domain\Orders\GraphQL\Shopify\Stable\Types\CheckoutBrandingGlobalInput),
            'header' => new \Spawnia\Sailor\Convert\NullConverter(new \App\Domain\Orders\GraphQL\Shopify\Stable\Types\CheckoutBrandingHeaderInput),
            'headingLevel1' => new \Spawnia\Sailor\Convert\NullConverter(new \App\Domain\Orders\GraphQL\Shopify\Stable\Types\CheckoutBrandingHeadingLevelInput),
            'headingLevel2' => new \Spawnia\Sailor\Convert\NullConverter(new \App\Domain\Orders\GraphQL\Shopify\Stable\Types\CheckoutBrandingHeadingLevelInput),
            'headingLevel3' => new \Spawnia\Sailor\Convert\NullConverter(new \App\Domain\Orders\GraphQL\Shopify\Stable\Types\CheckoutBrandingHeadingLevelInput),
            'main' => new \Spawnia\Sailor\Convert\NullConverter(new \App\Domain\Orders\GraphQL\Shopify\Stable\Types\CheckoutBrandingMainInput),
            'merchandiseThumbnail' => new \Spawnia\Sailor\Convert\NullConverter(new \App\Domain\Orders\GraphQL\Shopify\Stable\Types\CheckoutBrandingMerchandiseThumbnailInput),
            'orderSummary' => new \Spawnia\Sailor\Convert\NullConverter(new \App\Domain\Orders\GraphQL\Shopify\Stable\Types\CheckoutBrandingOrderSummaryInput),
            'primaryButton' => new \Spawnia\Sailor\Convert\NullConverter(new \App\Domain\Orders\GraphQL\Shopify\Stable\Types\CheckoutBrandingButtonInput),
            'secondaryButton' => new \Spawnia\Sailor\Convert\NullConverter(new \App\Domain\Orders\GraphQL\Shopify\Stable\Types\CheckoutBrandingButtonInput),
            'select' => new \Spawnia\Sailor\Convert\NullConverter(new \App\Domain\Orders\GraphQL\Shopify\Stable\Types\CheckoutBrandingSelectInput),
            'textField' => new \Spawnia\Sailor\Convert\NullConverter(new \App\Domain\Orders\GraphQL\Shopify\Stable\Types\CheckoutBrandingTextFieldInput),
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
