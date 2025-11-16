<?php declare(strict_types=1);

namespace App\Domain\Orders\GraphQL\Shopify\Stable\Types;

/**
 * @property string|null $accessActivityLog
 * @property string|null $cancellationPolicyDisclosure
 * @property \App\Domain\Orders\GraphQL\Shopify\Stable\Types\ShopifyPaymentsDisputeFileUploadUpdateInput|null $cancellationPolicyFile
 * @property string|null $cancellationRebuttal
 * @property \App\Domain\Orders\GraphQL\Shopify\Stable\Types\ShopifyPaymentsDisputeFileUploadUpdateInput|null $customerCommunicationFile
 * @property string|null $customerEmailAddress
 * @property string|null $customerFirstName
 * @property string|null $customerLastName
 * @property string|null $refundPolicyDisclosure
 * @property \App\Domain\Orders\GraphQL\Shopify\Stable\Types\ShopifyPaymentsDisputeFileUploadUpdateInput|null $refundPolicyFile
 * @property string|null $refundRefusalExplanation
 * @property \App\Domain\Orders\GraphQL\Shopify\Stable\Types\ShopifyPaymentsDisputeFileUploadUpdateInput|null $serviceDocumentationFile
 * @property \App\Domain\Orders\GraphQL\Shopify\Stable\Types\MailingAddressInput|null $shippingAddress
 * @property \App\Domain\Orders\GraphQL\Shopify\Stable\Types\ShopifyPaymentsDisputeFileUploadUpdateInput|null $shippingDocumentationFile
 * @property bool|null $submitEvidence
 * @property \App\Domain\Orders\GraphQL\Shopify\Stable\Types\ShopifyPaymentsDisputeFileUploadUpdateInput|null $uncategorizedFile
 * @property string|null $uncategorizedText
 */
class ShopifyPaymentsDisputeEvidenceUpdateInput extends \Spawnia\Sailor\ObjectLike
{
    /**
     * @param string|null $accessActivityLog
     * @param string|null $cancellationPolicyDisclosure
     * @param \App\Domain\Orders\GraphQL\Shopify\Stable\Types\ShopifyPaymentsDisputeFileUploadUpdateInput|null $cancellationPolicyFile
     * @param string|null $cancellationRebuttal
     * @param \App\Domain\Orders\GraphQL\Shopify\Stable\Types\ShopifyPaymentsDisputeFileUploadUpdateInput|null $customerCommunicationFile
     * @param string|null $customerEmailAddress
     * @param string|null $customerFirstName
     * @param string|null $customerLastName
     * @param string|null $refundPolicyDisclosure
     * @param \App\Domain\Orders\GraphQL\Shopify\Stable\Types\ShopifyPaymentsDisputeFileUploadUpdateInput|null $refundPolicyFile
     * @param string|null $refundRefusalExplanation
     * @param \App\Domain\Orders\GraphQL\Shopify\Stable\Types\ShopifyPaymentsDisputeFileUploadUpdateInput|null $serviceDocumentationFile
     * @param \App\Domain\Orders\GraphQL\Shopify\Stable\Types\MailingAddressInput|null $shippingAddress
     * @param \App\Domain\Orders\GraphQL\Shopify\Stable\Types\ShopifyPaymentsDisputeFileUploadUpdateInput|null $shippingDocumentationFile
     * @param bool|null $submitEvidence
     * @param \App\Domain\Orders\GraphQL\Shopify\Stable\Types\ShopifyPaymentsDisputeFileUploadUpdateInput|null $uncategorizedFile
     * @param string|null $uncategorizedText
     */
    public static function make(
        $accessActivityLog = 'Special default value that allows Sailor to differentiate between explicitly passing null and not passing a value at all.',
        $cancellationPolicyDisclosure = 'Special default value that allows Sailor to differentiate between explicitly passing null and not passing a value at all.',
        $cancellationPolicyFile = 'Special default value that allows Sailor to differentiate between explicitly passing null and not passing a value at all.',
        $cancellationRebuttal = 'Special default value that allows Sailor to differentiate between explicitly passing null and not passing a value at all.',
        $customerCommunicationFile = 'Special default value that allows Sailor to differentiate between explicitly passing null and not passing a value at all.',
        $customerEmailAddress = 'Special default value that allows Sailor to differentiate between explicitly passing null and not passing a value at all.',
        $customerFirstName = 'Special default value that allows Sailor to differentiate between explicitly passing null and not passing a value at all.',
        $customerLastName = 'Special default value that allows Sailor to differentiate between explicitly passing null and not passing a value at all.',
        $refundPolicyDisclosure = 'Special default value that allows Sailor to differentiate between explicitly passing null and not passing a value at all.',
        $refundPolicyFile = 'Special default value that allows Sailor to differentiate between explicitly passing null and not passing a value at all.',
        $refundRefusalExplanation = 'Special default value that allows Sailor to differentiate between explicitly passing null and not passing a value at all.',
        $serviceDocumentationFile = 'Special default value that allows Sailor to differentiate between explicitly passing null and not passing a value at all.',
        $shippingAddress = 'Special default value that allows Sailor to differentiate between explicitly passing null and not passing a value at all.',
        $shippingDocumentationFile = 'Special default value that allows Sailor to differentiate between explicitly passing null and not passing a value at all.',
        $submitEvidence = 'Special default value that allows Sailor to differentiate between explicitly passing null and not passing a value at all.',
        $uncategorizedFile = 'Special default value that allows Sailor to differentiate between explicitly passing null and not passing a value at all.',
        $uncategorizedText = 'Special default value that allows Sailor to differentiate between explicitly passing null and not passing a value at all.',
    ): self {
        $instance = new self;

        if ($accessActivityLog !== self::UNDEFINED) {
            $instance->accessActivityLog = $accessActivityLog;
        }
        if ($cancellationPolicyDisclosure !== self::UNDEFINED) {
            $instance->cancellationPolicyDisclosure = $cancellationPolicyDisclosure;
        }
        if ($cancellationPolicyFile !== self::UNDEFINED) {
            $instance->cancellationPolicyFile = $cancellationPolicyFile;
        }
        if ($cancellationRebuttal !== self::UNDEFINED) {
            $instance->cancellationRebuttal = $cancellationRebuttal;
        }
        if ($customerCommunicationFile !== self::UNDEFINED) {
            $instance->customerCommunicationFile = $customerCommunicationFile;
        }
        if ($customerEmailAddress !== self::UNDEFINED) {
            $instance->customerEmailAddress = $customerEmailAddress;
        }
        if ($customerFirstName !== self::UNDEFINED) {
            $instance->customerFirstName = $customerFirstName;
        }
        if ($customerLastName !== self::UNDEFINED) {
            $instance->customerLastName = $customerLastName;
        }
        if ($refundPolicyDisclosure !== self::UNDEFINED) {
            $instance->refundPolicyDisclosure = $refundPolicyDisclosure;
        }
        if ($refundPolicyFile !== self::UNDEFINED) {
            $instance->refundPolicyFile = $refundPolicyFile;
        }
        if ($refundRefusalExplanation !== self::UNDEFINED) {
            $instance->refundRefusalExplanation = $refundRefusalExplanation;
        }
        if ($serviceDocumentationFile !== self::UNDEFINED) {
            $instance->serviceDocumentationFile = $serviceDocumentationFile;
        }
        if ($shippingAddress !== self::UNDEFINED) {
            $instance->shippingAddress = $shippingAddress;
        }
        if ($shippingDocumentationFile !== self::UNDEFINED) {
            $instance->shippingDocumentationFile = $shippingDocumentationFile;
        }
        if ($submitEvidence !== self::UNDEFINED) {
            $instance->submitEvidence = $submitEvidence;
        }
        if ($uncategorizedFile !== self::UNDEFINED) {
            $instance->uncategorizedFile = $uncategorizedFile;
        }
        if ($uncategorizedText !== self::UNDEFINED) {
            $instance->uncategorizedText = $uncategorizedText;
        }

        return $instance;
    }

    protected function converters(): array
    {
        static $converters;

        return $converters ??= [
            'accessActivityLog' => new \Spawnia\Sailor\Convert\NullConverter(new \Spawnia\Sailor\Convert\StringConverter),
            'cancellationPolicyDisclosure' => new \Spawnia\Sailor\Convert\NullConverter(new \Spawnia\Sailor\Convert\StringConverter),
            'cancellationPolicyFile' => new \Spawnia\Sailor\Convert\NullConverter(new \App\Domain\Orders\GraphQL\Shopify\Stable\Types\ShopifyPaymentsDisputeFileUploadUpdateInput),
            'cancellationRebuttal' => new \Spawnia\Sailor\Convert\NullConverter(new \Spawnia\Sailor\Convert\StringConverter),
            'customerCommunicationFile' => new \Spawnia\Sailor\Convert\NullConverter(new \App\Domain\Orders\GraphQL\Shopify\Stable\Types\ShopifyPaymentsDisputeFileUploadUpdateInput),
            'customerEmailAddress' => new \Spawnia\Sailor\Convert\NullConverter(new \Spawnia\Sailor\Convert\StringConverter),
            'customerFirstName' => new \Spawnia\Sailor\Convert\NullConverter(new \Spawnia\Sailor\Convert\StringConverter),
            'customerLastName' => new \Spawnia\Sailor\Convert\NullConverter(new \Spawnia\Sailor\Convert\StringConverter),
            'refundPolicyDisclosure' => new \Spawnia\Sailor\Convert\NullConverter(new \Spawnia\Sailor\Convert\StringConverter),
            'refundPolicyFile' => new \Spawnia\Sailor\Convert\NullConverter(new \App\Domain\Orders\GraphQL\Shopify\Stable\Types\ShopifyPaymentsDisputeFileUploadUpdateInput),
            'refundRefusalExplanation' => new \Spawnia\Sailor\Convert\NullConverter(new \Spawnia\Sailor\Convert\StringConverter),
            'serviceDocumentationFile' => new \Spawnia\Sailor\Convert\NullConverter(new \App\Domain\Orders\GraphQL\Shopify\Stable\Types\ShopifyPaymentsDisputeFileUploadUpdateInput),
            'shippingAddress' => new \Spawnia\Sailor\Convert\NullConverter(new \App\Domain\Orders\GraphQL\Shopify\Stable\Types\MailingAddressInput),
            'shippingDocumentationFile' => new \Spawnia\Sailor\Convert\NullConverter(new \App\Domain\Orders\GraphQL\Shopify\Stable\Types\ShopifyPaymentsDisputeFileUploadUpdateInput),
            'submitEvidence' => new \Spawnia\Sailor\Convert\NullConverter(new \Spawnia\Sailor\Convert\BooleanConverter),
            'uncategorizedFile' => new \Spawnia\Sailor\Convert\NullConverter(new \App\Domain\Orders\GraphQL\Shopify\Stable\Types\ShopifyPaymentsDisputeFileUploadUpdateInput),
            'uncategorizedText' => new \Spawnia\Sailor\Convert\NullConverter(new \Spawnia\Sailor\Convert\StringConverter),
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
