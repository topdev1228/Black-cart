<?php declare(strict_types=1);

namespace App\Domain\Orders\GraphQL\Shopify\Stable\Types;

/**
 * @property bool|null $checkoutToDraft
 * @property bool|null $editableShippingAddress
 * @property int|string|null $paymentTermsTemplateId
 */
class BuyerExperienceConfigurationInput extends \Spawnia\Sailor\ObjectLike
{
    /**
     * @param bool|null $checkoutToDraft
     * @param bool|null $editableShippingAddress
     * @param int|string|null $paymentTermsTemplateId
     */
    public static function make(
        $checkoutToDraft = 'Special default value that allows Sailor to differentiate between explicitly passing null and not passing a value at all.',
        $editableShippingAddress = 'Special default value that allows Sailor to differentiate between explicitly passing null and not passing a value at all.',
        $paymentTermsTemplateId = 'Special default value that allows Sailor to differentiate between explicitly passing null and not passing a value at all.',
    ): self {
        $instance = new self;

        if ($checkoutToDraft !== self::UNDEFINED) {
            $instance->checkoutToDraft = $checkoutToDraft;
        }
        if ($editableShippingAddress !== self::UNDEFINED) {
            $instance->editableShippingAddress = $editableShippingAddress;
        }
        if ($paymentTermsTemplateId !== self::UNDEFINED) {
            $instance->paymentTermsTemplateId = $paymentTermsTemplateId;
        }

        return $instance;
    }

    protected function converters(): array
    {
        static $converters;

        return $converters ??= [
            'checkoutToDraft' => new \Spawnia\Sailor\Convert\NullConverter(new \Spawnia\Sailor\Convert\BooleanConverter),
            'editableShippingAddress' => new \Spawnia\Sailor\Convert\NullConverter(new \Spawnia\Sailor\Convert\BooleanConverter),
            'paymentTermsTemplateId' => new \Spawnia\Sailor\Convert\NullConverter(new \Spawnia\Sailor\Convert\IDConverter),
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
