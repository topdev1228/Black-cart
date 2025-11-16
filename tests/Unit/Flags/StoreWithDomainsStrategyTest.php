<?php

declare(strict_types=1);

namespace Tests\Unit\Flags;

use App\Domain\Stores\Models\Store;
use App\Domain\Stores\Values\Store as StoreValue;
use App\Flags\StoreWithDomainsStrategy;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\App;
use Tests\TestCase;

class StoreWithDomainsStrategyTest extends TestCase
{
    protected StoreValue $store;

    protected function setUp(): void
    {
        parent::setUp();

        $this->store = StoreValue::from(
            Store::withoutEvents(function () {
                return Store::factory()->create([
                    'domain' => 'testing.myshopify.com',
                ]);
            })
        );

        App::context(store: $this->store);
    }

    public function testEnabledSetting()
    {
        $strategy = new StoreWithDomainsStrategy();
        $this->assertTrue($strategy->isEnabled(
            [
                'storeDomains' => 'testing.myshopify.com,testing2.myshopify.com',
            ],
            $this->createMock(Request::class),
            $this->store,
        ));

        $this->assertTrue($strategy->isEnabled(
            [
                'storeDomains' => 'testing.myshopify.com,testing2.myshopify.com',
            ],
            $this->createMock(Request::class),
            // Test that by passing null, it'll use the store in the context
        ));
    }

    public function testDisabledSetting()
    {
        $strategy = new StoreWithDomainsStrategy();
        $this->assertFalse($strategy->isEnabled(
            [
                'storeDomains' => 'testing1.myshopify.com,testing2.myshopify.com',
            ],
            $this->createMock(Request::class),
            $this->store,
        ));

        $this->assertFalse($strategy->isEnabled(
            [
                'storeDomains' => 'testing1.myshopify.com,testing2.myshopify.com',
            ],
            $this->createMock(Request::class),
            // Test that by passing null, it'll use the store in the context
        ));
    }
}
