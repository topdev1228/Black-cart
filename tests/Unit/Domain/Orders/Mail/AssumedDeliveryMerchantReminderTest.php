<?php

declare(strict_types=1);

namespace Tests\Unit\Domain\Orders\Mail;

use App\Domain\Orders\Mail\AssumedDeliveryMerchantReminder;
use App\Domain\Orders\Values\Order as OrderValue;
use Illuminate\Mail\Mailables\Content;
use Illuminate\Mail\Mailables\Envelope;
use Illuminate\Support\Facades\Http;
use Tests\TestCase;

class AssumedDeliveryMerchantReminderTest extends TestCase
{
    protected function setUp(): void
    {
        parent::setUp();
        Http::fake([
            'http://localhost:8080/api/stores/settings' => Http::response([
                'settings' => [
                    'customerSupportEmail' => [
                        'value' => 'customersupport@merchant.com',
                    ],
                ],
            ], 200),
        ]);
    }

    public function testItCanBeInstantiated(): void
    {
        $order = $this->createMock(OrderValue::class);
        $order->method('orderName')->willReturn('12345');
        $merchantEmail = 'yuriyleve@gmail.com';

        $mail = new AssumedDeliveryMerchantReminder($order, $merchantEmail);

        $this->assertInstanceOf(AssumedDeliveryMerchantReminder::class, $mail);
    }

    public function testEnvelopeReturnsCorrectEnvelope(): void
    {
        $order = $this->createMock(OrderValue::class);
        $order->method('orderName')->willReturn('12345');
        $merchantEmail = 'yuriyleve@gmail.com';

        $mail = new AssumedDeliveryMerchantReminder($order, $merchantEmail);

        $envelope = $mail->envelope();

        $this->assertInstanceOf(Envelope::class, $envelope);
        $this->assertEquals(__('No delivery notification for Order :orderNumber', ['orderNumber' => '12345']), $envelope->subject);
    }

    public function testItSetsOrderNumberCorrectly(): void
    {
        $order = $this->createMock(OrderValue::class);
        $order->method('orderName')->willReturn('12345');
        $merchantEmail = 'yuriyleve@gmail.com';

        $mail = new AssumedDeliveryMerchantReminder($order, $merchantEmail);

        $this->assertEquals('12345', $mail->orderNumber);
    }

    public function testItSetsLayoutContentCorrectly(): void
    {
        $order = $this->createMock(OrderValue::class);
        $order->method('orderName')->willReturn('12345');
        $merchantEmail = 'yuriyleve@gmail.com';

        $mail = new AssumedDeliveryMerchantReminder($order, $merchantEmail);

        $this->assertEquals(__('No delivery notification recorded'), $mail->layoutContent->title);
        $this->assertEquals('green-exclamation.png', $mail->layoutContent->headerIcon);
        $this->assertEquals('12345', $mail->layoutContent->orderNumber);
    }

    public function testContentReturnsCorrectContent(): void
    {
        $order = $this->createMock(OrderValue::class);
        $order->method('orderName')->willReturn('12345');
        $merchantEmail = 'yuriyleve@gmail.com';

        $mail = new AssumedDeliveryMerchantReminder($order, $merchantEmail);

        $content = $mail->content();

        $this->assertInstanceOf(Content::class, $content);
        $this->assertEquals('Orders::mail.assumed-delivery-merchant-reminder', $content->markdown);
        $this->assertEquals([
            'layoutContent' => $mail->layoutContent,
            'firstName' => $mail->store->name,
        ], $content->with);
    }
}
