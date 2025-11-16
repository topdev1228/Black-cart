<?php
declare(strict_types=1);

namespace App\Domain\Orders\Mail;

use App;
use App\Domain\Orders\Values\Order as OrderValue;
use App\Domain\Shared\Mail\BaseMail;
use App\Domain\Shared\Values\MailLayoutContent;
use Brick\Money\Money;
use Carbon\CarbonImmutable;
use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailables\Attachment;
use Illuminate\Mail\Mailables\Content;
use Illuminate\Mail\Mailables\Envelope;
use Illuminate\Queue\SerializesModels;

class ReAuthFailed extends BaseMail
{
    use Queueable;
    use SerializesModels;

    protected MailLayoutContent $layoutContent;
    protected string $orderNumber;
    public string $merchantName;

    public function __construct(protected OrderValue $order, protected Money $authAmount, protected CarbonImmutable $authExpiry)
    {
        parent::__construct();

        $store = App::context()->store;
        $this->orderNumber = $this->order->orderName();
        $this->merchantName = $store->name;

        $this->layoutContent = new MailLayoutContent(
            title: __('Your payment was processed because re-authorization failed'),
            headerIcon: 'failed.png',
            orderNumber: $this->orderNumber,
            companyName: $store->name,
            companyContact: $this->storeEmail,
        );
    }

    /**
     * Get the message envelope.
     */
    public function envelope(): Envelope
    {
        return new Envelope(
            subject: __(':merchantName order :orderNumber your card re-authorization failed', [
                'orderNumber' => $this->orderNumber,
                'merchantName' => $this->merchantName,
            ]),
        );
    }

    /**
     * Get the message content definition.
     */
    public function content(): Content
    {
        return new Content(
            markdown: 'Orders::mail.re-auth-failed',
            with: [
                'layoutContent' => $this->layoutContent,
                'firstName' => $this->order->customerFirstName(),
                'authAmount' => $this->authAmount->formatTo('en_US', true),
                'authExpiry' => $this->authExpiry->toDateString(),
            ]
        );
    }

    /**
     * Get the attachments for the message.
     *
     * @return array<int, Attachment>
     */
    public function attachments(): array
    {
        return [];
    }
}
