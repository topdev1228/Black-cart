<?php
declare(strict_types=1);

namespace Tests\Feature\Domain\Orders\Listeners;

use App\Domain\Orders\Listeners\AddBlackcartTagsAfterOrderCreatedListener;
use App\Domain\Orders\Models\Order;
use App\Domain\Orders\Values\Order as OrderValue;
use App\Domain\Orders\Values\OrderCreatedEvent as OrderCreatedEventValue;
use App\Domain\Shopify\Exceptions\InternalShopifyRequestException;
use App\Domain\Stores\Models\Store;
use App\Domain\Stores\Values\Store as StoreValue;
use Illuminate\Database\Eloquent\ModelNotFoundException;
use Illuminate\Http\Client\Request;
use Illuminate\Support\Facades\App;
use Illuminate\Support\Facades\Http;
use PHPUnit\Framework\Attributes\DataProvider;
use Tests\Fixtures\Domains\Orders\Traits\ShopifyAddTagsResponsesTestData;
use Tests\Fixtures\Domains\Shared\Shopify\Traits\ShopifyErrorsTestData;
use Tests\TestCase;

class AddBlackcartTagsAfterOrderCreatedListenerTest extends TestCase
{
    use ShopifyAddTagsResponsesTestData;
    use ShopifyErrorsTestData;

    protected Store $store;

    protected function setUp(): void
    {
        parent::setUp();

        $this->store = Store::withoutEvents(function () {
            return Store::factory()->create();
        });
        App::context(store: StoreValue::from($this->store));
    }

    public function testItDoesNotTagOrderOnOrderNotFound(): void
    {
        Http::fake();

        $order = Order::withoutEvents(function () {
            return Order::factory()->tbybOnly(10000, 10000)
                ->netSales(10000, 10000, 0, 0)->create(['store_id' => $this->store->id]);
        });
        $orderValue = OrderValue::from($order);
        $orderValue->id = 'non-existent-id';

        $event = new OrderCreatedEventValue($orderValue);

        $this->expectException(ModelNotFoundException::class);

        $listener = resolve(AddBlackcartTagsAfterOrderCreatedListener::class);
        $listener->handle($event);

        Http::assertNothingSent();
    }

    #[DataProvider('shopifyErrorExceptionsForMutationProvider')]
    public function testItDoesNotTagOrderOnShopifyErrors(
        array $responseJson,
        int $httpStatusCode,
        string $expectedException,
    ): void {
        Http::fake([
            App::context()->store->domain . '/admin/api/*/graphql.json' => Http::sequence()
                ->push($responseJson, $httpStatusCode),
        ]);
        // We catch all Shopify exceptions and throw InternalShopifyRequestException instead
        $this->expectException(InternalShopifyRequestException::class);

        $order = Order::withoutEvents(function () {
            return Order::factory()->tbybOnly(10000, 10000)
                ->netSales(10000, 10000, 0, 0)->create(['store_id' => $this->store->id]);
        });
        $orderValue = OrderValue::from($order);

        $event = new OrderCreatedEventValue($orderValue);
        $listener = resolve(AddBlackcartTagsAfterOrderCreatedListener::class);
        $listener->handle($event);

        Http::assertSent(function (Request $request) {
            // TODO: validate graphql query
            return $request->method() === 'POST' &&
                $request->hasHeader('X-Shopify-Access-Token', App::context()->store->accessToken);
        });
    }

    public function testItTagsOrder(): void
    {
        Http::fake([
            App::context()->store->domain . '/admin/api/*/graphql.json' => Http::sequence()
                ->push($this->getShopifyAddTagsSuccessResponse()),
        ]);

        $order = Order::withoutEvents(function () {
            return Order::factory()->tbybOnly(10000, 10000)
                ->netSales(10000, 10000, 0, 0)->create(['store_id' => $this->store->id]);
        });
        $orderValue = OrderValue::from($order);

        $event = new OrderCreatedEventValue($orderValue);
        $listener = resolve(AddBlackcartTagsAfterOrderCreatedListener::class);
        $listener->handle($event);

        Http::assertSent(function (Request $request) {
            // TODO: validate graphql query
            return $request->method() === 'POST' &&
                $request->hasHeader('X-Shopify-Access-Token', App::context()->store->accessToken);
        });
    }
}
