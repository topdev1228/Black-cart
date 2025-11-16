<?php
declare(strict_types=1);

namespace Tests\Unit\Domain\Payments\Mail;

use App\Domain\Payments\Mail\ReAuthNotice;
use App\Domain\Payments\Values\Order as OrderValue;
use App\Domain\Stores\Models\Store;
use App\Domain\Stores\Values\Store as StoreValue;
use Brick\Money\Money;
use Illuminate\Support\Facades\App;
use Illuminate\Support\Facades\Date;
use Illuminate\Support\Facades\Http;
use Tests\TestCase;

class ReAuthNoticeEmailTest extends TestCase
{
    protected $complexOrderJson;
    protected $currentStore;

    protected $order;

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

        $this->complexOrderJson = collect($this->loadFixtureData('order-complex.json', 'Payments'));
        $this->currentStore = StoreValue::from(Store::withoutEvents(function () {
            return Store::factory()->create();
        }));

        $this->order = OrderValue::builder()->create([
            'orderData' => $this->complexOrderJson,
        ]);
        App::context(store: $this->currentStore);
    }

    public function testOrderDeliveredEmailDisplaysOrderContent(): void
    {
        $mailable = new ReAuthNotice($this->order, Money::of(150, 'USD'), Date::now()->addDays(2));

        $mailable->assertSeeInHtml('Your card will be re-authorized soon');
        $mailable->assertSeeInHtml($this->order->orderName());
        $mailable->assertSeeInHtml($this->order->customerFirstName());
        $mailable->assertSeeInHtml('$150');
        $mailable->assertDontSeeInHtml('Your Blackcart Order');
    }
}
