<?php declare(strict_types=1);

namespace App\Domain\Orders\GraphQL\Shopify\Stable\Operations\RefundCreate;

class RefundCreateResult extends \Spawnia\Sailor\Result
{
    public ?RefundCreate $data = null;

    protected function setData(\stdClass $data): void
    {
        $this->data = RefundCreate::fromStdClass($data);
    }

    /**
     * Useful for instantiation of successful mocked results.
     *
     * @return static
     */
    public static function fromData(RefundCreate $data): self
    {
        $instance = new static;
        $instance->data = $data;

        return $instance;
    }

    public function errorFree(): RefundCreateErrorFreeResult
    {
        return RefundCreateErrorFreeResult::fromResult($this);
    }

    public static function endpoint(): string
    {
        return 'shopify-orders-2024-01';
    }

    public static function config(): string
    {
        return \Safe\realpath(__DIR__ . '/../../../../../../../../sailor.php');
    }
}
