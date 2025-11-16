<?php
declare(strict_types=1);

namespace App\Domain\Payments\Mail;

use App;
use App\Domain\Payments\Values\Order as OrderValue;
use App\Domain\Shared\Mail\BaseMail;
use App\Domain\Shared\Values\MailLayoutContent;
use Brick\Money\Money;
use Carbon\CarbonImmutable;
use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailables\Attachment;
use Illuminate\Mail\Mailables\Content;
use Illuminate\Mail\Mailables\Envelope;
use Illuminate\Queue\SerializesModels;

class ReAuthNotice extends BaseMail
{
    use Queueable;
    use SerializesModels;

    protected MailLayoutContent $layoutContent;
    protected string $orderNumber;

    public string $merchantName;

    /**
     * Create a new message instance.
     */
    public function __construct(protected OrderValue $order, protected Money $authAmount, protected CarbonImmutable $authExpiry)
    {
        parent::__construct();

        $store = App::context()->store;
        $this->merchantName = $store->name;
        $this->orderNumber = $this->order->orderName();

        $this->layoutContent = new MailLayoutContent(
            title: __('Your card will be re-authorized soon'),
            headerIcon: 're-authorization.png',
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
            subject: __(':merchantName order :orderNumber your card will be re-authorized soon', [
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
            markdown: 'Payments::mail.re-auth-notice',
            with: [
                'layoutContent' => $this->layoutContent,
                'order' => $this->order,
                'firstName' => $this->order->customerFirstName(),
                'authAmount' => $this->authAmount->formatTo('en_US', true),
                'authExpiry' => $this->authExpiry->format('m-d-Y'),
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
