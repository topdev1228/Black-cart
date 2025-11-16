<?php
declare(strict_types=1);

namespace Tests\Unit\Domain\Billings\Services;

use App;
use App\Domain\Billings\Events\BillableChargesCreatedEvent;
use App\Domain\Billings\Models\Charge;
use App\Domain\Billings\Repositories\ChargeRepository;
use App\Domain\Billings\Repositories\UsageConfigRepository;
use App\Domain\Billings\Services\ChargeService;
use App\Domain\Billings\Services\ShopifySubscriptionService;
use App\Domain\Billings\Services\SubscriptionService;
use App\Domain\Billings\Services\UsageConfigService;
use App\Domain\Billings\Values\Charge as ChargeValue;
use App\Domain\Billings\Values\Subscription as SubscriptionValue;
use App\Domain\Billings\Values\TbybNetSale as TbybNetSaleValue;
use App\Domain\Billings\Values\UsageConfig;
use App\Domain\Stores\Models\Store;
use Brick\Math\RoundingMode;
use Brick\Money\Money;
use Carbon\CarbonImmutable;
use Event;
use Mockery\MockInterface;
use PHPUnit\Framework\Attributes\DataProvider;
use PrinsFrank\Standards\Currency\CurrencyAlpha3;
use Tests\TestCase;

class ChargeServiceTest extends TestCase
{
    #[DataProvider('chargesDataProvider')]
    public function testItCreatesCharges(Money $tbybNetSales, Money $previousTotal, int $numberOfCharges, Money $totalCharges, Money $netSalesTotal, array $expectedCharges = null): void
    {
        CarbonImmutable::setTestNow(CarbonImmutable::create(2024, 5, 1));

        Event::fake([BillableChargesCreatedEvent::class]);

        $store = Store::factory(['id' => 1])->create();
        App::context(store: $store);

        $this->mock(UsageConfigRepository::class, function (MockInterface $mock) use ($store) {
            $mock->expects('get')->andReturn(UsageConfig::from([
                'storeId' => $store->id,
                'description' => 'Test',
                'currency' => 'USD',
                'config' => json_decode(<<<'EOF'
                    [
                      {
                        "description": "First $2500 included in subscription fee",
                        "start": 0,
                        "end": 250000,
                        "step": 250000,
                        "price": 0,
                        "currency": "USD"
                      },
                      {
                        "description": "$2500.01 - $100,000 at $100/$2500 (4%)",
                        "start": 250001,
                        "end": 10000000,
                        "step": 250000,
                        "price": 10000,
                        "currency": "USD"
                      },
                      {
                        "description": ">$200,000.01 at $50/$2500 (2%)",
                        "start": 20000001,
                        "end": null,
                        "step": 250000,
                        "price": 5000,
                        "currency": "USD"
                      },
                      {
                        "description": "$100,000.01 - $200,000 at $800/$25000 (3.2%)",
                        "start": 10000001,
                        "end": 20000000,
                        "step": 2500000,
                        "price": 80000,
                        "currency": "USD"
                      }
                    ]
                    EOF, true, 512, JSON_THROW_ON_ERROR),
                'subscriptionLineItemId' => '1',
                'validFrom' => CarbonImmutable::now()->subDay(),
                'validTo' => null,
            ]));
        });

        $this->mock(ChargeRepository::class, function (MockInterface $mock) use ($numberOfCharges, $previousTotal) {
            if ($previousTotal->isZero()) {
                $mock->expects('getPriorByTimeRangeEnd')->never();
            } else {
                $mock->expects('getPriorByTimeRangeEnd')->andReturn(ChargeValue::builder()->create(['balance' => $previousTotal]));
            }

            $mock->expects('create')->times($numberOfCharges)->passthru();
        });

        $tbybNetSaleValue = TbybNetSaleValue::builder()->create([
            'id' => 'tbyb_net_sale_id',
            'time_range_start' => CarbonImmutable::now()->subDays(1),
            'time_range_end' => CarbonImmutable::now(),
            'tbyb_net_sales' => $tbybNetSales,
            'is_first_of_billing_period' => $previousTotal->isZero(),
        ]);

        $chargeService = $this->app->make(ChargeService::class);
        $charges = $chargeService->createCharges($tbybNetSaleValue);

        // Confirm no duplicate steps
        $steps = [];
        $charges->get('charges')->each(function (ChargeValue $charge) use ($steps) {
            $key = $charge->stepStartAmount->getMinorAmount()->toInt() . '-' . $charge->stepEndAmount->getMinorAmount()->toInt();
            $this->assertArrayNotHasKey($key, $steps);
            $steps[$key] = true;
        });

        $this->assertCount($numberOfCharges, $charges->get('charges'));
        $this->assertDatabaseCount('billings_charges', $numberOfCharges);
        /** @var Money $sum */
        $sum = $charges['charges']->reduce(function (Money $sum, ChargeValue $charge) use ($previousTotal) {
            // $previousTotal->isZero() is used to determine if there was a previous charge specifically for these test cases
            $this->assertEquals($previousTotal->isZero(), $charge->isFirstOfBillingPeriod);

            return $sum->plus($charge->amount);
        }, Money::zero('USD'));
        $this->assertTrue($totalCharges->isEqualTo($sum), sprintf('Total charges do not match (%s expected vs %s actual)', $totalCharges->formatTo('en_US', true), $sum->formatTo('en_US', true)));
        $this->assertTrue($charges['net_sales_total']->isEqualTo($netSalesTotal), sprintf('Net sales total do not match (%s expected vs %s actual)', $netSalesTotal->formatTo('en_US', true), $charges['net_sales_total']->formatTo('en_US', true)));

        $expectedCharges = collect($expectedCharges);
        $expectedCharges->each(function (array $expectedCharge, int $index) use ($netSalesTotal, $charges) {
            $charge = $charges['charges'][$index];
            $this->assertEquals($expectedCharge['currency'], $charge->currency->value);
            $this->assertTrue(
                $charge->amount->isEqualTo(Money::of($expectedCharge['amount'], $charge->currency->value)),
                sprintf('Charge #%d: amount does not match %s (expected) vs %s (actual)', $index, Money::of($expectedCharge['amount'], $charge->currency->value)->formatTo('en_US', true), $charge->amount->formatTo('en_US', true))
            );
            $this->assertTrue(
                $charge->balance->isEqualTo($netSalesTotal),
                sprintf('Charge #%d: balance does not match %s (expected) vs %s (actual)', $index, $netSalesTotal->formatTo('en_US', true), $charge->balance->formatTo('en_US', true))
            );
            $this->assertTrue(
                $charge->stepSize->isEqualTo(Money::of($expectedCharge['step_size'], $charge->currency->value)),
                sprintf('Charge #%d: stepSize does not match %s (expected) vs %s (actual)', $index, Money::of($expectedCharge['step_size'], $charge->currency->value)->formatTo('en_US', true), $charge->stepSize->formatTo('en_US', true))
            );
            $this->assertTrue(
                $charge->stepStartAmount->isEqualTo(Money::of($expectedCharge['step_start_amount'], $charge->currency->value)),
                sprintf('Charge #%d: stepStartAmount does not match %s (expected) vs %s (actual)', $index, Money::of($expectedCharge['step_start_amount'], $charge->currency->value)->formatTo('en_US', true), $charge->stepStartAmount->formatTo('en_US', true))
            );
            $this->assertTrue(
                $charge->stepEndAmount->isEqualTo(Money::of($expectedCharge['step_end_amount'], $charge->currency->value)),
                sprintf('Charge #%d: stepEndAmount does not match %s (expected) vs %s (actual)', $index, Money::of($expectedCharge['step_end_amount'], $charge->currency->value)->formatTo('en_US', true), $charge->stepEndAmount->formatTo('en_US', true))
            );
            $this->assertFalse($charge->isBilled);
            $this->assertNull($charge->total, sprintf('Charge #%d: total is not null', $index));
        });

        if ($numberOfCharges > 0) {
            Event::assertDispatched(BillableChargesCreatedEvent::class);
        }
    }

    public function testItChargesWhenNoIncludedSales(): void
    {
        CarbonImmutable::setTestNow(CarbonImmutable::create(2024, 5, 1));

        Event::fake([BillableChargesCreatedEvent::class]);

        $store = Store::factory()->create();
        App::context(store: $store);

        $this->mock(UsageConfigRepository::class, function (MockInterface $mock) {
            $mock->expects('get')->andReturn(UsageConfig::from([
                'storeId' => '1',
                'description' => 'Test',
                'currency' => 'USD',
                'config' => json_decode(<<<'EOF'
                    [
                      {
                        "description": "First $2500 at $10",
                        "start": 0,
                        "end": 250000,
                        "step": 250000,
                        "price": 1000,
                        "currency": "USD"
                      },
                      {
                        "description": "$2500.01 - $100,000 at $100/$2500 (4%)",
                        "start": 250001,
                        "end": 10000000,
                        "step": 250000,
                        "price": 10000,
                        "currency": "USD"
                      },
                      {
                        "description": ">$200,000.01 at $50/$2500 (2%)",
                        "start": 20000001,
                        "end": null,
                        "step": 250000,
                        "price": 5000,
                        "currency": "USD"
                      },
                      {
                        "description": "$100,000.01 - $200,000 at $800/$25000 (3.2%)",
                        "start": 10000001,
                        "end": 20000000,
                        "step": 2500000,
                        "price": 80000,
                        "currency": "USD"
                      }
                    ]
                    EOF, true, 512, JSON_THROW_ON_ERROR),
                'subscriptionLineItemId' => '1',
                'validFrom' => CarbonImmutable::now()->subDay(),
                'validTo' => null,
            ]));
        });

        $this->mock(ChargeRepository::class, function (MockInterface $mock) {
            $mock->expects('getPriorByTimeRangeEnd')->never();
            $mock->expects('create')->twice()->passthru();
        });

        $tbybNetSaleValue = TbybNetSaleValue::builder()->create([
            'id' => 'tbyb_net_sale_id',
            'time_range_start' => CarbonImmutable::now()->subDays(1),
            'time_range_end' => CarbonImmutable::now(),
            'tbyb_net_sales' => Money::of(2600, 'USD'),
            'is_first_of_billing_period' => true,
        ]);

        $chargeService = $this->app->make(ChargeService::class);
        $charges = $chargeService->createCharges($tbybNetSaleValue);

        $this->assertCount(2, $charges->get('charges'));

        $this->assertDatabaseCount('billings_charges', 2);
        /** @var Money $sum */
        $sum = $charges['charges']->reduce(function (Money $sum, ChargeValue $charge) {
            $this->assertTrue($charge->isFirstOfBillingPeriod);

            return $sum->plus($charge->amount);
        }, Money::zero('USD'));
        $this->assertTrue(Money::of(110, 'USD')->isEqualTo($sum), sprintf('Total charges do not match (%s expected vs %s actual)', Money::of(110, 'USD')->formatTo('en_US', true), $sum->formatTo('en_US', true)));
        $this->assertTrue($charges['net_sales_total']->isEqualTo(Money::of(2600, 'USD')), sprintf('Net sales total do not match (%s expected vs %s actual)', Money::of(2600, 'USD')->formatTo('en_US', true), $charges['net_sales_total']->formatTo('en_US', true)));

        Event::assertDispatched(BillableChargesCreatedEvent::class);
    }

    public function testItInitiatesBilling(): void
    {
        CarbonImmutable::setTestNow(CarbonImmutable::create(2024, 5, 1));

        $store = Store::factory()->create();
        App::context(store: $store);

        $this->mock(UsageConfigRepository::class, function (MockInterface $mock) {
            $mock->expects('get')->andReturn(UsageConfig::from([
                'storeId' => '1',
                'description' => 'Test',
                'currency' => 'USD',
                'config' => json_decode(<<<'EOF'
                    [
                      {
                        "description": "First $2500 at $10",
                        "start": 0,
                        "end": 250000,
                        "step": 250000,
                        "price": 1000,
                        "currency": "USD"
                      },
                      {
                        "description": "$2500.01 - $100,000 at $100/$2500 (4%)",
                        "start": 250001,
                        "end": 10000000,
                        "step": 250000,
                        "price": 10000,
                        "currency": "USD"
                      },
                      {
                        "description": ">$200,000.01 at $50/$2500 (2%)",
                        "start": 20000001,
                        "end": null,
                        "step": 250000,
                        "price": 5000,
                        "currency": "USD"
                      },
                      {
                        "description": "$100,000.01 - $200,000 at $800/$25000 (3.2%)",
                        "start": 10000001,
                        "end": 20000000,
                        "step": 2500000,
                        "price": 80000,
                        "currency": "USD"
                      }
                    ]
                    EOF, true, 512, JSON_THROW_ON_ERROR),
                'subscriptionLineItemId' => '1',
                'validFrom' => CarbonImmutable::now()->subDay(),
                'validTo' => null,
            ]));
        });

        $this->mock(ChargeRepository::class, function (MockInterface $mock) {
            $mock->expects('getPriorByTimeRangeEnd')->never();
            $mock->expects('create')->twice()->passthru();
        });

        $this->mock(ChargeService::class, function (MockInterface $mock) {
            $mock->expects('__construct')->passthru();
            $mock->__construct(
                $this->app->make(ChargeRepository::class),
                $this->app->make(UsageConfigService::class),
                $this->app->make(ShopifySubscriptionService::class),
                $this->app->make(SubscriptionService::class),
            );
            $mock->expects('createCharges')->passthru();
            $mock->expects('billCharges')->once();
        });

        $tbybNetSaleValue = TbybNetSaleValue::builder()->create([
            'id' => 'tbyb_net_sale_id',
            'time_range_start' => CarbonImmutable::now()->subDays(1),
            'time_range_end' => CarbonImmutable::now(),
            'tbyb_net_sales' => Money::of(2600, 'USD'),
            'is_first_of_billing_period' => true,
        ]);

        $chargeService = $this->app->make(ChargeService::class);
        $charges = $chargeService->createCharges($tbybNetSaleValue);

        $this->assertCount(2, $charges->get('charges'));

        $this->assertDatabaseCount('billings_charges', 2);
        /** @var Money $sum */
        $sum = $charges['charges']->reduce(function (Money $sum, ChargeValue $charge) {
            $this->assertTrue($charge->isFirstOfBillingPeriod);

            return $sum->plus($charge->amount);
        }, Money::zero('USD'));
        $this->assertTrue(Money::of(110, 'USD')->isEqualTo($sum), sprintf('Total charges do not match (%s expected vs %s actual)', Money::of(110, 'USD')->formatTo('en_US', true), $sum->formatTo('en_US', true)));
        $this->assertTrue($charges['net_sales_total']->isEqualTo(Money::of(2600, 'USD')), sprintf('Net sales total do not match (%s expected vs %s actual)', Money::of(2600, 'USD')->formatTo('en_US', true), $charges['net_sales_total']->formatTo('en_US', true)));
    }

    public function testItCreatesChargesResetPreviousBalanceOnNewBillingPeriodStart(): void
    {
        CarbonImmutable::setTestNow(CarbonImmutable::create(2024, 5, 1));

        $store = Store::factory()->create();
        App::context(store: $store);

        $this->mock(UsageConfigRepository::class, function (MockInterface $mock) use ($store) {
            $mock->expects('get')->andReturn(UsageConfig::from([
                'storeId' => $store->id,
                'description' => 'Test',
                'currency' => 'USD',
                'config' => json_decode(<<<'EOF'
                    [
                      {
                        "description": "First $2500 included",
                        "start": 0,
                        "end": 250000,
                        "step": 250000,
                        "price": 0,
                        "currency": "USD"
                      },
                      {
                        "description": "$2500.01 - $100,000 at $100/$2500 (4%)",
                        "start": 250001,
                        "end": 10000000,
                        "step": 250000,
                        "price": 10000,
                        "currency": "USD"
                      },
                      {
                        "description": ">$200,000.01 at $50/$2500 (2%)",
                        "start": 20000001,
                        "end": null,
                        "step": 250000,
                        "price": 5000,
                        "currency": "USD"
                      },
                      {
                        "description": "$100,000.01 - $200,000 at $800/$25000 (3.2%)",
                        "start": 10000001,
                        "end": 20000000,
                        "step": 2500000,
                        "price": 80000,
                        "currency": "USD"
                      }
                    ]
                    EOF, true, 512, JSON_THROW_ON_ERROR),
                'subscriptionLineItemId' => '1',
                'validFrom' => CarbonImmutable::now()->subDay(),
                'validTo' => null,
            ]));
        });

        $this->mock(ChargeRepository::class, function (MockInterface $mock) {
            // This call is skipped on the first day of every billing period
            $mock->expects('getPriorByTimeRangeEnd')->never();

            $mock->expects('create')->twice()->passthru();
        });

        $this->mock(ChargeService::class, function (MockInterface $mock) {
            $mock->expects('__construct')->passthru();
            $mock->__construct(
                $this->app->make(ChargeRepository::class),
                $this->app->make(UsageConfigService::class),
                $this->app->make(ShopifySubscriptionService::class),
                $this->app->make(SubscriptionService::class),
            );
            $mock->expects('createCharges')->passthru();
            $mock->expects('billCharges')->once();
        });

        $tbybNetSaleValue = TbybNetSaleValue::builder()->create([
            'id' => 'tbyb_net_sale_id',
            'time_range_start' => CarbonImmutable::now()->subDays(1),
            'time_range_end' => CarbonImmutable::now(),
            'tbyb_net_sales' => Money::of(5200, 'USD'),
            'is_first_of_billing_period' => true,
        ]);

        $chargeService = $this->app->make(ChargeService::class);
        $charges = $chargeService->createCharges($tbybNetSaleValue);

        $this->assertCount(2, $charges->get('charges'));

        $this->assertDatabaseCount('billings_charges', 2);
        /** @var Money $sum */
        $sum = $charges['charges']->reduce(function (Money $sum, ChargeValue $charge) {
            $this->assertTrue($charge->isFirstOfBillingPeriod);

            return $sum->plus($charge->amount);
        }, Money::zero('USD'));
        $this->assertTrue(Money::of(200, 'USD')->isEqualTo($sum), sprintf('Total charges do not match (%s expected vs %s actual)', Money::of(200, 'USD')->formatTo('en_US', true), $sum->formatTo('en_US', true)));
        $this->assertTrue($charges['net_sales_total']->isEqualTo(Money::of(5200, 'USD')), sprintf('Net sales total do not match (%s expected vs %s actual)', Money::of(5200, 'USD')->formatTo('en_US', true), $charges['net_sales_total']->formatTo('en_US', true)));
    }

    public function testItCreatesChargesUsingPriorBalance(): void
    {
        CarbonImmutable::setTestNow(CarbonImmutable::create(2024, 5, 1));

        $store = Store::factory()->create();
        App::context(store: $store);

        Charge::withoutEvents(function () use ($store) {
            // Prior charge should return this record
            Charge::factory()->create([
                'store_id' => $store->id,
                'is_billed' => true,
                'amount' => 10000,
                'currency' => 'USD',
                'step_size' => 250000,
                'step_start_amount' => 250000,
                'step_end_amount' => 500000,
                'balance' => 410000,
                'time_range_start' => CarbonImmutable::now()->subDays(2),
                'time_range_end' => CarbonImmutable::now()->subDay(),
                'is_first_of_billing_period' => false,
            ]);

            // This is in the future, the prior charge shouldn't return this
            Charge::factory()->create([
                'store_id' => $store->id,
                'is_billed' => true,
                'amount' => 15000,
                'currency' => 'USD',
                'step_size' => 250000,
                'step_start_amount' => 0,
                'step_end_amount' => 250000,
                'balance' => 15000,
                'time_range_start' => CarbonImmutable::now(),
                'time_range_end' => CarbonImmutable::now()->addDay(),
                'is_first_of_billing_period' => true,
            ]);
        });

        $this->mock(UsageConfigRepository::class, function (MockInterface $mock) use ($store) {
            $mock->expects('get')->andReturn(UsageConfig::from([
                'storeId' => $store->id,
                'description' => 'Test',
                'currency' => 'USD',
                'config' => json_decode(<<<'EOF'
                    [
                      {
                        "description": "First $2500 included",
                        "start": 0,
                        "end": 250000,
                        "step": 250000,
                        "price": 0,
                        "currency": "USD"
                      },
                      {
                        "description": "$2500.01 - $100,000 at $100/$2500 (4%)",
                        "start": 250001,
                        "end": 10000000,
                        "step": 250000,
                        "price": 10000,
                        "currency": "USD"
                      },
                      {
                        "description": ">$200,000.01 at $50/$2500 (2%)",
                        "start": 20000001,
                        "end": null,
                        "step": 250000,
                        "price": 5000,
                        "currency": "USD"
                      },
                      {
                        "description": "$100,000.01 - $200,000 at $800/$25000 (3.2%)",
                        "start": 10000001,
                        "end": 20000000,
                        "step": 2500000,
                        "price": 80000,
                        "currency": "USD"
                      }
                    ]
                    EOF, true, 512, JSON_THROW_ON_ERROR),
                'subscriptionLineItemId' => '1',
                'validFrom' => CarbonImmutable::now()->subDay(),
                'validTo' => null,
            ]));
        });

        $this->mock(ChargeService::class, function (MockInterface $mock) {
            $mock->expects('__construct')->passthru();
            $mock->__construct(
                $this->app->make(ChargeRepository::class),
                $this->app->make(UsageConfigService::class),
                $this->app->make(ShopifySubscriptionService::class),
                $this->app->make(SubscriptionService::class),
            );
            $mock->expects('createCharges')->passthru();
            $mock->expects('billCharges')->once();
        });

        $tbybNetSaleValue = TbybNetSaleValue::builder()->create([
            'id' => 'tbyb_net_sale_id',
            'time_range_start' => CarbonImmutable::now()->subDay(),
            'time_range_end' => CarbonImmutable::now(),
            'tbyb_net_sales' => Money::of(5200, 'USD'),
            'is_first_of_billing_period' => false,
        ]);

        $this->assertDatabaseCount('billings_charges', 2);

        $chargeService = $this->app->make(ChargeService::class);
        $charges = $chargeService->createCharges($tbybNetSaleValue);

        $this->assertCount(2, $charges->get('charges'));
        $this->assertDatabaseCount('billings_charges', 4);

        $this->assertDatabaseHas('billings_charges', [
            'tbyb_net_sale_id' => 'tbyb_net_sale_id',
            'amount' => 10000,
            'balance' => 930000,
            'is_billed' => false,
            'billed_at' => null,
            'step_start_amount' => 500000,
            'step_end_amount' => 750000,
            'created_at' => CarbonImmutable::now()->toDateTimeString(),
            'time_range_start' => CarbonImmutable::now()->subDay()->toIso8601String(),
            'time_range_end' => CarbonImmutable::now()->toIso8601String(),
            'is_first_of_billing_period' => false,
        ]);

        $this->assertDatabaseHas('billings_charges', [
            'tbyb_net_sale_id' => 'tbyb_net_sale_id',
            'amount' => 10000,
            'balance' => 930000,
            'is_billed' => false,
            'billed_at' => null,
            'step_start_amount' => 750000,
            'step_end_amount' => 1000000,
            'created_at' => CarbonImmutable::now()->toDateTimeString(),
            'time_range_start' => CarbonImmutable::now()->subDay()->toIso8601String(),
            'time_range_end' => CarbonImmutable::now()->toIso8601String(),
            'is_first_of_billing_period' => false,
        ]);

        /** @var Money $sum */
        $sum = $charges['charges']->reduce(function (Money $sum, ChargeValue $charge) {
            $this->assertFalse($charge->isFirstOfBillingPeriod);

            return $sum->plus($charge->amount);
        }, Money::zero('USD'));
        $this->assertTrue(Money::of(200, 'USD')->isEqualTo($sum), sprintf('Total charges do not match (%s expected vs %s actual)', Money::of(200, 'USD')->formatTo('en_US', true), $sum->formatTo('en_US', true)));
        $this->assertTrue($charges['net_sales_total']->isEqualTo(Money::of(9300, 'USD')), sprintf('Net sales total do not match (%s expected vs %s actual)', Money::of(9300, 'USD')->formatTo('en_US', true), $charges['net_sales_total']->formatTo('en_US', true)));
    }

    public function testItBillsCharges(): void
    {
        $this->mock(SubscriptionService::class, function (MockInterface $mock) {
            $mock->expects('getActiveSubscription')->andReturn(SubscriptionValue::builder()->create(['store_id' => 'test-store-id']));
        });

        $this->mock(ShopifySubscriptionService::class, function (MockInterface $mock) {
            $mock->expects('addUsage')->withArgs(function (string $description, Money $amount) {
                $this->assertTrue(
                    match (true) {
                        $description === '$2,500 in TBYB net sales @ $100 x 3 ($2,500 - $10,000)' && $amount->isEqualTo(Money::of(300, 'USD')) => true,
                        $description === '$25,000 in TBYB net sales @ $82.50 x 2 ($100,000 - $150,000)' && $amount->isEqualTo(Money::of(165, 'USD')) => true,
                        default => false,
                    }
                );

                return true;
            })->twice();
        });

        $this->mock(UsageConfigRepository::class, function (MockInterface $mock) {
            $mock->expects('get')->andReturn(UsageConfig::from([
                'storeId' => '1',
                'description' => 'Test',
                'currency' => 'USD',
                'config' => json_decode(<<<'EOF'
                    [
                      {
                        "description": "First $2500 included in subscription fee",
                        "start": 0,
                        "end": 250000,
                        "step": 250000,
                        "price": 0,
                        "currency": "USD"
                      },
                      {
                        "description": "$2500.01 - $100,000 at $100/$2500 (4%)",
                        "start": 250001,
                        "end": 10000000,
                        "step": 250000,
                        "price": 10000,
                        "currency": "USD"
                      },
                      {
                        "description": ">$200,000.01 at $50/$2500 (2%)",
                        "start": 20000001,
                        "end": null,
                        "step": 250000,
                        "price": 5000,
                        "currency": "USD"
                      },
                      {
                        "description": "$100,000.01 - $200,000 at $800/$25000 (3.2%)",
                        "start": 10000001,
                        "end": 20000000,
                        "step": 2500000,
                        "price": 80000,
                        "currency": "USD"
                      }
                    ]
                    EOF, true, 512, JSON_THROW_ON_ERROR),
                'subscriptionLineItemId' => '1',
                'validFrom' => CarbonImmutable::now()->subDay(),
                'validTo' => null,
            ]));
        });

        $store = Store::factory()->create();
        App::context(store: $store);

        Charge::factory(['store_id' => $store->id, 'is_billed' => false, 'amount' => 10000, 'currency' => 'USD', 'step_size' => 250000, 'step_start_amount' => 250000, 'step_end_amount' => 1000000])->count(3)->create();
        Charge::factory(['store_id' => $store->id, 'is_billed' => false, 'amount' => 8250, 'currency' => 'USD', 'step_size' => 2500000, 'step_start_amount' => 10000000, 'step_end_amount' => 15000000])->count(2)->create();
        Charge::factory(['store_id' => $store->id, 'is_billed' => true, 'amount' => 30000, 'currency' => 'USD', 'step_size' => 250000])->count(1)->create();

        $chargeService = resolve(ChargeService::class);
        $charges = $chargeService->billCharges();

        $this->assertCount(2, $charges);

        $this->assertEquals('$2,500 in TBYB net sales @ $100 x 3 ($2,500 - $10,000)', $charges[0]->description);
        $this->assertTrue($charges[0]->total->isEqualTo(Money::ofMinor(30000, 'USD')));
        $this->assertEquals(CurrencyAlpha3::US_Dollar, $charges[0]->currency);
        $this->assertEquals(3, $charges[0]->quantity);

        $this->assertEquals('$25,000 in TBYB net sales @ $82.50 x 2 ($100,000 - $150,000)', $charges[1]->description);
        $this->assertTrue($charges[1]->total->isEqualTo(Money::ofMinor(16500, 'USD')));
        $this->assertEquals(CurrencyAlpha3::US_Dollar, $charges[1]->currency);
        $this->assertEquals(2, $charges[1]->quantity);
    }

    public function testItDoesNotBillsZeroChargesDuringTrial(): void
    {
        $this->mock(SubscriptionService::class, function (MockInterface $mock) {
            $mock->expects('getActiveSubscription')->andReturn(SubscriptionValue::builder()->create(['store_id' => 'test-store-id', 'trial_period_end' => CarbonImmutable::now()->addDays(29)]));
        });

        $this->mock(ShopifySubscriptionService::class, function (MockInterface $mock) {
            $mock->expects('addUsage')->never();
        });

        $this->mock(UsageConfigRepository::class, function (MockInterface $mock) {
            $mock->expects('get')->andReturn(UsageConfig::from([
                'storeId' => '1',
                'description' => 'Test',
                'currency' => 'USD',
                'config' => json_decode(<<<'EOF'
                    [
                      {
                        "description": "First $2500 included in subscription fee",
                        "start": 0,
                        "end": 250000,
                        "step": 250000,
                        "price": 0,
                        "currency": "USD"
                      },
                      {
                        "description": "$2500.01 - $100,000 at $100/$2500 (4%)",
                        "start": 250001,
                        "end": 10000000,
                        "step": 250000,
                        "price": 10000,
                        "currency": "USD"
                      },
                      {
                        "description": ">$200,000.01 at $50/$2500 (2%)",
                        "start": 20000001,
                        "end": null,
                        "step": 250000,
                        "price": 5000,
                        "currency": "USD"
                      },
                      {
                        "description": "$100,000.01 - $200,000 at $800/$25000 (3.2%)",
                        "start": 10000001,
                        "end": 20000000,
                        "step": 2500000,
                        "price": 80000,
                        "currency": "USD"
                      }
                    ]
                    EOF, true, 512, JSON_THROW_ON_ERROR),
                'subscriptionLineItemId' => '1',
                'validFrom' => CarbonImmutable::now()->subDay(),
                'validTo' => null,
            ]));
        });

        $store = Store::factory()->create();
        App::context(store: $store);

        Charge::factory(['store_id' => $store->id, 'is_billed' => false, 'amount' => 10000, 'currency' => 'USD', 'step_size' => 250000, 'step_start_amount' => 250000, 'step_end_amount' => 1000000])->count(3)->create();
        Charge::factory(['store_id' => $store->id, 'is_billed' => false, 'amount' => 8250, 'currency' => 'USD', 'step_size' => 2500000, 'step_start_amount' => 10000000, 'step_end_amount' => 15000000])->count(2)->create();
        Charge::factory(['store_id' => $store->id, 'is_billed' => true, 'amount' => 30000, 'currency' => 'USD', 'step_size' => 250000])->count(1)->create();

        $chargeService = resolve(ChargeService::class);
        $charges = $chargeService->billCharges();

        $this->assertCount(2, $charges);

        $this->assertEquals('$2,500 in TBYB net sales @ $100 x 3 ($2,500 - $10,000) (Trial)', $charges[0]->description);
        $this->assertTrue($charges[0]->total->isEqualTo(Money::zero('USD')));
        $this->assertEquals(CurrencyAlpha3::US_Dollar, $charges[0]->currency);
        $this->assertEquals(3, $charges[0]->quantity);

        $this->assertEquals('$25,000 in TBYB net sales @ $82.50 x 2 ($100,000 - $150,000) (Trial)', $charges[1]->description);
        $this->assertTrue($charges[1]->total->isEqualTo(Money::zero('USD')));
        $this->assertEquals(CurrencyAlpha3::US_Dollar, $charges[1]->currency);
        $this->assertEquals(2, $charges[1]->quantity);
    }

    public function testItSupportsMultipleCurrencies(): void
    {
        CarbonImmutable::setTestNow(CarbonImmutable::create(2024, 5, 1));

        Event::fake([BillableChargesCreatedEvent::class]);

        $store = Store::factory()->create();
        App::context(store: $store);

        $this->mock(UsageConfigRepository::class, function (MockInterface $mock) {
            $mock->expects('get')->andReturn(UsageConfig::from([
                'storeId' => '1',
                'description' => 'Test',
                'currency' => 'USD',
                'config' => json_decode(<<<'EOF'
                    [
                      {
                        "description": "First $2500 at $0",
                        "start": 0,
                        "end": 250000,
                        "step": 250000,
                        "price": 0,
                        "currency": "USD"
                      },
                      {
                        "description": "$2500.01 - $100,000 at $100/$2500 (4%)",
                        "start": 250001,
                        "end": 10000000,
                        "step": 250000,
                        "price": 10000,
                        "currency": "USD"
                      },
                      {
                        "description": ">$200,000.01 at $50/$2500 (2%)",
                        "start": 20000001,
                        "end": null,
                        "step": 250000,
                        "price": 5000,
                        "currency": "USD"
                      },
                      {
                        "description": "$100,000.01 - $200,000 at $800/$25000 (3.2%)",
                        "start": 10000001,
                        "end": 20000000,
                        "step": 2500000,
                        "price": 80000,
                        "currency": "USD"
                      }
                    ]
                    EOF, true, 512, JSON_THROW_ON_ERROR),
                'subscriptionLineItemId' => '1',
                'validFrom' => CarbonImmutable::now()->subDay(),
                'validTo' => null,
            ]));
        });

        $this->mock(ChargeRepository::class, function (MockInterface $mock) {
            $mock->expects('getPriorByTimeRangeEnd')->never();
            $mock->expects('create')->once()->passthru();
        });

        $tbybNetSaleValue = TbybNetSaleValue::builder()->create([
            'id' => 'tbyb_net_sale_id',
            'time_range_start' => CarbonImmutable::now()->subDays(1),
            'time_range_end' => CarbonImmutable::now(),
            'tbyb_net_sales' => Money::of(2600, 'CAD')->multipliedBy('1.345309', config('money.rounding')),
            'is_first_of_billing_period' => true,
        ]);

        $chargeService = $this->app->make(ChargeService::class);
        $charges = $chargeService->createCharges($tbybNetSaleValue);

        $this->assertCount(1, $charges->get('charges'));

        $this->assertDatabaseCount('billings_charges', 1);
        /** @var Money $sum */
        $sum = $charges['charges']->reduce(function (Money $sum, ChargeValue $charge) {
            $this->assertTrue($charge->isFirstOfBillingPeriod);

            return $sum->plus($charge->amount);
        }, Money::zero('USD'));
        $this->assertTrue(Money::of(100, 'USD')->isEqualTo($sum), sprintf('Total charges do not match (%s expected vs %s actual)', Money::of(110, 'USD')->formatTo('en_US', true), $sum->formatTo('en_US', true)));
        $this->assertTrue($charges['net_sales_total']->isEqualTo(Money::of(2600, 'USD')), sprintf('Net sales total do not match (%s expected vs %s actual)', Money::of(2600, 'USD')->formatTo('en_US', true), $charges['net_sales_total']->formatTo('en_US', true)));

        Event::assertDispatched(BillableChargesCreatedEvent::class);
    }

    public function testItDoesNotDuplicatePartialStep(): void
    {
        $today = CarbonImmutable::now();
        $yesterday = CarbonImmutable::now()->subDay();
        $tomorrow = CarbonImmutable::now()->addDay();

        Event::fake([BillableChargesCreatedEvent::class]);

        $store = Store::factory(['id' => 1])->create();
        App::context(store: $store);

        $this->mock(UsageConfigRepository::class, function (MockInterface $mock) {
            $mock->expects('get')->twice()->andReturn(UsageConfig::from([
                'storeId' => '1',
                'description' => 'Test',
                'currency' => 'USD',
                'config' => json_decode(<<<'EOF'
                    [
                      {
                        "description": "First $2500 at $10",
                        "start": 0,
                        "end": 250000,
                        "step": 250000,
                        "price": 1000,
                        "currency": "USD"
                      },
                      {
                        "description": "$2500.01 - $100,000 at $100/$2500 (4%)",
                        "start": 250001,
                        "end": 10000000,
                        "step": 250000,
                        "price": 10000,
                        "currency": "USD"
                      },
                      {
                        "description": ">$200,000.01 at $50/$2500 (2%)",
                        "start": 20000001,
                        "end": null,
                        "step": 250000,
                        "price": 5000,
                        "currency": "USD"
                      },
                      {
                        "description": "$100,000.01 - $200,000 at $800/$25000 (3.2%)",
                        "start": 10000001,
                        "end": 20000000,
                        "step": 2500000,
                        "price": 80000,
                        "currency": "USD"
                      }
                    ]
                    EOF, true, 512, JSON_THROW_ON_ERROR),
                'subscriptionLineItemId' => '1',
                'validFrom' => CarbonImmutable::now()->subDay(),
                'validTo' => null,
            ]));
        });

        CarbonImmutable::setTestNow($yesterday);
        Charge::factory([
            'store_id' => 1,
            'is_billed' => true,
            'billed_at' => $yesterday->subDay(),
            'amount' => 10000,
            'currency' => 'USD',
            'step_size' => 250000,
            'balance' => '606145',
            'step_start_amount' => 250000,
            'step_end_amount' => 500000,
            'time_range_start' => $yesterday->subDays(2),
            'time_range_end' => $yesterday->subDay(),
        ])->create();
        Charge::factory([
            'store_id' => 1,
            'is_billed' => true,
            'billed_at' => $yesterday,
            'amount' => 10000,
            'currency' => 'USD',
            'balance' => '606145',
            'step_size' => 250000,
            'step_start_amount' => 500000,
            'step_end_amount' => 750000,
            'time_range_start' => $yesterday->subDay(),
            'time_range_end' => $yesterday,
        ])->create();

        CarbonImmutable::setTestNow($today);

        $tbybNetSaleValue = TbybNetSaleValue::builder()->create([
            'id' => 'tbyb_net_sale_id',
            'time_range_start' => $yesterday,
            'time_range_end' => CarbonImmutable::now(),
            'tbyb_net_sales' => Money::ofMinor(292583, 'USD'),
            'is_first_of_billing_period' => false,
        ]);

        $chargeService = $this->app->make(ChargeService::class);
        $chargeService->createCharges($tbybNetSaleValue);

        $this->assertDatabaseCount('billings_charges', 3);
        $this->assertDatabaseHas('billings_charges', [
            'amount' => 10000,
            'balance' => 606145,
            'is_billed' => true,
            'billed_at' => $yesterday->subDay()->toDateTimeString(),
            'step_start_amount' => 250000,
            'step_end_amount' => 500000,
            'created_at' => $yesterday->toDateTime(),
            'time_range_start' => $yesterday->subDays(2)->toDateTimeString(),
            'time_range_end' => $yesterday->subDay()->toDateTimeString(),
        ]);
        $this->assertDatabaseHas('billings_charges', [
            'amount' => 10000,
            'balance' => 606145,
            'is_billed' => true,
            'billed_at' => $yesterday->toDateTimeString(),
            'step_start_amount' => 500000,
            'step_end_amount' => 750000,
            'created_at' => $yesterday->toDateTime(),
            'time_range_start' => $yesterday->subDay()->toDateTimeString(),
            'time_range_end' => $yesterday->toDateTimeString(),
        ]);
        $this->assertDatabaseHas('billings_charges', [
            'tbyb_net_sale_id' => 'tbyb_net_sale_id',
            'amount' => 10000,
            'balance' => 898728,
            'is_billed' => false,
            'billed_at' => null,
            'step_start_amount' => 750000,
            'step_end_amount' => 1000000,
            'created_at' => $today->toDateTime(),
        ]);

        CarbonImmutable::setTestNow($tomorrow);

        $tbybNetSaleValue = TbybNetSaleValue::builder()->create([
            'id' => 'tbyb_net_sale_id_2',
            'time_range_start' => $today,
            'time_range_end' => $tomorrow,
            'tbyb_net_sales' => Money::ofMinor(907412, 'USD'),
            'is_first_of_billing_period' => false,
        ]);

        $chargeService = $this->app->make(ChargeService::class);
        $chargeService->createCharges($tbybNetSaleValue);

        $this->assertDatabaseCount('billings_charges', 7);
        $this->assertDatabaseHas('billings_charges', [
            'amount' => 10000,
            'balance' => 606145,
            'is_billed' => true,
            'step_start_amount' => 250000,
            'step_end_amount' => 500000,
            'created_at' => $yesterday->toDateTime(),
        ]);
        $this->assertDatabaseHas('billings_charges', [
            'amount' => 10000,
            'balance' => 606145,
            'is_billed' => true,
            'step_start_amount' => 500000,
            'step_end_amount' => 750000,
            'created_at' => $yesterday->toDateTime(),
        ]);
        $this->assertDatabaseHas('billings_charges', [
            'tbyb_net_sale_id' => 'tbyb_net_sale_id',
            'amount' => 10000,
            'balance' => 898728,
            'is_billed' => false,
            'billed_at' => null,
            'step_start_amount' => 750000,
            'step_end_amount' => 1000000,
            'created_at' => $today->toDateTime(),
        ]);
        $this->assertDatabaseHas('billings_charges', [
            'tbyb_net_sale_id' => 'tbyb_net_sale_id_2',
            'amount' => 10000,
            'balance' => 1806140,
            'is_billed' => false,
            'billed_at' => null,
            'step_start_amount' => 1000000,
            'step_end_amount' => 1250000,
            'created_at' => $tomorrow->toDateTime(),
        ]);
        $this->assertDatabaseHas('billings_charges', [
            'tbyb_net_sale_id' => 'tbyb_net_sale_id_2',
            'amount' => 10000,
            'balance' => 1806140,
            'is_billed' => false,
            'billed_at' => null,
            'step_start_amount' => 1250000,
            'step_end_amount' => 1500000,
            'created_at' => $tomorrow->toDateTime(),
        ]);
        $this->assertDatabaseHas('billings_charges', [
            'tbyb_net_sale_id' => 'tbyb_net_sale_id_2',
            'amount' => 10000,
            'balance' => 1806140,
            'is_billed' => false,
            'billed_at' => null,
            'step_start_amount' => 1500000,
            'step_end_amount' => 1750000,
            'created_at' => $tomorrow->toDateTime(),
        ]);
        $this->assertDatabaseHas('billings_charges', [
            'tbyb_net_sale_id' => 'tbyb_net_sale_id_2',
            'amount' => 10000,
            'balance' => 1806140,
            'is_billed' => false,
            'billed_at' => null,
            'step_start_amount' => 1750000,
            'step_end_amount' => 2000000,
            'created_at' => $tomorrow->toDateTime(),
        ]);
    }

    public static function chargesDataProvider(): array
    {
        return [
            'less sales than initial credit, no previous balance' => [
                'tbybNetSales' => Money::of(1000, 'USD'),
                'previousTotal' => Money::zero('USD'),
                'numberOfCharges' => 1,
                'totalCharges' => Money::zero('USD'),
                'netSalesTotal' => Money::of(1000, 'USD'),
                'expectedCharges' => [
                    [
                        'currency' => 'USD',
                        'amount' => 0,
                        'step_size' => 0,
                        'step_start_amount' => 0,
                        'step_end_amount' => 0,
                        'store_id' => '1',
                    ],
                ],
            ],
            'negative sales with initial credit, no previous balance' => [
                'tbybNetSales' => Money::of(-1700, 'USD'),
                'previousTotal' => Money::zero('USD'),
                'numberOfCharges' => 1,
                'totalCharges' => Money::zero('USD'),
                'netSalesTotal' => Money::of(-1700, 'USD'),
                'expectedCharges' => [
                    [
                        'currency' => 'USD',
                        'amount' => 0,
                        'balance' => -1700,
                        'step_size' => 0,
                        'step_start_amount' => 0,
                        'step_end_amount' => 0,
                        'store_id' => '1',
                    ],
                ],
            ],
            'more sales than initial credit, one step, no previous balance' => [
                'tbybNetSales' => Money::of(3000, 'USD'),
                'previousTotal' => Money::zero('USD'),
                'numberOfCharges' => 1,
                'totalCharges' => Money::of(100, 'USD'),
                'netSalesTotal' => Money::of(3000, 'USD'),
                'expectedCharges' => [
                    [
                        'currency' => 'USD',
                        'amount' => 100,
                        'step_size' => 2500,
                        'step_start_amount' => 2500,
                        'step_end_amount' => 5000,
                        'store_id' => '1',
                    ],
                ],
            ],
            'more sales than initial credit, one step, with less refunds, no previous balance' => [
                'tbybNetSales' => Money::of(2700, 'USD'),
                'previousTotal' => Money::zero('USD'),
                'numberOfCharges' => 1,
                'totalCharges' => Money::of(100, 'USD'),
                'netSalesTotal' => Money::of(2700, 'USD'),
                'expectedCharges' => [
                    [
                        'currency' => 'USD',
                        'amount' => 100,
                        'step_size' => 2500,
                        'step_start_amount' => 2500,
                        'step_end_amount' => 5000,
                        'store_id' => '1',
                    ],
                ],
            ],
            'more sales than initial credit, refunds negative step, no previous balance' => [
                'tbybNetSales' => Money::of(2400, 'USD'),
                'previousTotal' => Money::zero('USD'),
                'numberOfCharges' => 1,
                'totalCharges' => Money::zero('USD'),
                'netSalesTotal' => Money::of(2400, 'USD'),
                'expectedCharges' => [
                    [
                        'currency' => 'USD',
                        'amount' => 0,
                        'step_size' => 0,
                        'step_start_amount' => 0,
                        'step_end_amount' => 0,
                        'store_id' => '1',
                    ],
                ],
            ],
            'more sales than initial credit, less than one step, with previous balance' => [
                'tbybNetSales' => Money::of(50, 'USD'),
                'previousTotal' => Money::of(2600, 'USD'),
                'numberOfCharges' => 1,
                'totalCharges' => Money::zero('USD'),
                'netSalesTotal' => Money::of(2650, 'USD'),
                'expectedCharges' => [
                    [
                        'currency' => 'USD',
                        'amount' => 0,
                        'step_size' => 2500,
                        'step_start_amount' => 0,
                        'step_end_amount' => 0,
                        'store_id' => '1',
                    ],
                ],
            ],
            'more sales than initial credit, one step, with previous balance' => [
                'tbybNetSales' => Money::of(3000, 'USD'),
                'previousTotal' => Money::of(2400, 'USD'),
                'numberOfCharges' => 2,
                'totalCharges' => Money::of(200, 'USD'),
                'netSalesTotal' => Money::of(5400, 'USD'),
                'expectedCharges' => [
                    [
                        'currency' => 'USD',
                        'amount' => 100,
                        'step_size' => 2500,
                        'step_start_amount' => 2500,
                        'step_end_amount' => 5000,
                        'store_id' => '1',
                    ],
                    [
                        'currency' => 'USD',
                        'amount' => 100,
                        'step_size' => 2500,
                        'step_start_amount' => 5000,
                        'step_end_amount' => 7500,
                        'store_id' => '1',
                    ],
                ],
            ],
            'multiple steps, single bracket, no previous balance' => [
                'tbybNetSales' => Money::of(31000, 'USD'),
                'previousTotal' => Money::zero('USD'),
                'numberOfCharges' => 12,
                'totalCharges' => Money::of(1200, 'USD'),
                'netSalesTotal' => Money::of(31000, 'USD'),
                'expectedCharges' => [
                    [
                        'currency' => 'USD',
                        'amount' => 100,
                        'step_size' => 2500,
                        'step_start_amount' => 2500,
                        'step_end_amount' => 5000,
                        'store_id' => '1',
                    ],
                    [
                        'currency' => 'USD',
                        'amount' => 100,
                        'step_size' => 2500,
                        'step_start_amount' => 5000,
                        'step_end_amount' => 7500,
                        'store_id' => '1',
                    ],
                    6 => [
                        'currency' => 'USD',
                        'amount' => 100,
                        'step_size' => 2500,
                        'step_start_amount' => 17500,
                        'step_end_amount' => 20000,
                        'store_id' => '1',
                    ],
                    9 => [
                        'currency' => 'USD',
                        'amount' => 100,
                        'step_size' => 2500,
                        'step_start_amount' => 25000,
                        'step_end_amount' => 27500,
                        'store_id' => '1',
                    ],
                    11 => [
                        'currency' => 'USD',
                        'amount' => 100,
                        'step_size' => 2500,
                        'step_start_amount' => 30000,
                        'step_end_amount' => 32500,
                        'store_id' => '1',
                    ],
                ],
            ],
            'multiple steps, single bracket, with refunds, no previous balance' => [
                'tbybNetSales' => Money::of(27600, 'USD'),
                'previousTotal' => Money::zero('USD'),
                'numberOfCharges' => 11,
                'totalCharges' => Money::of(1100, 'USD'),
                'netSalesTotal' => Money::of(27600, 'USD'),
                'expectedCharges' => [
                    [
                        'currency' => 'USD',
                        'amount' => 100,
                        'step_size' => 2500,
                        'step_start_amount' => 2500,
                        'step_end_amount' => 5000,
                        'store_id' => '1',
                    ],
                    [
                        'currency' => 'USD',
                        'amount' => 100,
                        'step_size' => 2500,
                        'step_start_amount' => 5000,
                        'step_end_amount' => 7500,
                        'store_id' => '1',
                    ],
                    6 => [
                        'currency' => 'USD',
                        'amount' => 100,
                        'step_size' => 2500,
                        'step_start_amount' => 17500,
                        'step_end_amount' => 20000,
                        'store_id' => '1',
                    ],
                    10 => [
                        'currency' => 'USD',
                        'amount' => 100,
                        'step_size' => 2500,
                        'step_start_amount' => 27500,
                        'step_end_amount' => 30000,
                        'store_id' => '1',
                    ],
                ],
            ],
            'multiple steps, multiple brackets, no refunds, no previous balance' => [
                'tbybNetSales' => Money::of(284000, 'USD'), // $282,500.01 - $285,000 bracket
                'previousTotal' => Money::zero('USD'),
                'numberOfCharges' => 77, // ((100000-2500) / 2500 * 100) (39 lines, $3,900) + (100000/25000 * 800) = (4 lines + $3,3200) + (85000/2500 * 50) = (34 lines + $1750)
                'totalCharges' => Money::of(8800, 'USD'),
                'netSalesTotal' => Money::of(284000, 'USD'),
                'expectedCharges' => [
                    [
                        'currency' => 'USD',
                        'amount' => 100,
                        'step_size' => 2500,
                        'step_start_amount' => 2500,
                        'step_end_amount' => 5000,
                        'store_id' => '1',
                    ],
                    [
                        'currency' => 'USD',
                        'amount' => 100,
                        'step_size' => 2500,
                        'step_start_amount' => 5000,
                        'step_end_amount' => 7500,
                        'store_id' => '1',
                    ],
                    6 => [
                        'currency' => 'USD',
                        'amount' => 100,
                        'step_size' => 2500,
                        'step_start_amount' => 17500,
                        'step_end_amount' => 20000,
                        'store_id' => '1',
                    ],
                    10 => [
                        'currency' => 'USD',
                        'amount' => 100,
                        'step_size' => 2500,
                        'step_start_amount' => 27500,
                        'step_end_amount' => 30000,
                        'store_id' => '1',
                    ],
                    39 => [
                        'currency' => 'USD',
                        'amount' => 800,
                        'step_size' => 25000,
                        'step_start_amount' => 100000,
                        'step_end_amount' => 125000,
                        'store_id' => '1',
                    ],
                    40 => [
                        'currency' => 'USD',
                        'amount' => 800,
                        'step_size' => 25000,
                        'step_start_amount' => 125000,
                        'step_end_amount' => 150000,
                        'store_id' => '1',
                    ],
                    41 => [
                        'currency' => 'USD',
                        'amount' => 800,
                        'step_size' => 25000,
                        'step_start_amount' => 150000,
                        'step_end_amount' => 175000,
                        'store_id' => '1',
                    ],
                    42 => [
                        'currency' => 'USD',
                        'amount' => 800,
                        'step_size' => 25000,
                        'step_start_amount' => 175000,
                        'step_end_amount' => 200000,
                        'store_id' => '1',
                    ],
                    43 => [
                        'currency' => 'USD',
                        'amount' => 50,
                        'step_size' => 2500,
                        'step_start_amount' => 200000,
                        'step_end_amount' => 202500,
                        'store_id' => '1',
                    ],
                    59 => [
                        'currency' => 'USD',
                        'amount' => 50,
                        'step_size' => 2500,
                        'step_start_amount' => 240000,
                        'step_end_amount' => 242500,
                        'store_id' => '1',
                    ],
                    66 => [
                        'currency' => 'USD',
                        'amount' => 50,
                        'step_size' => 2500,
                        'step_start_amount' => 257500,
                        'step_end_amount' => 260000,
                        'store_id' => '1',
                    ],
                    72 => [
                        'currency' => 'USD',
                        'amount' => 50,
                        'step_size' => 2500,
                        'step_start_amount' => 272500,
                        'step_end_amount' => 275000,
                        'store_id' => '1',
                    ],
                    76 => [
                        'currency' => 'USD',
                        'amount' => 50,
                        'step_size' => 2500,
                        'step_start_amount' => 282500,
                        'step_end_amount' => 285000,
                        'store_id' => '1',
                    ],
                ],
            ],
            'multiple steps, multiple brackets, with refunds, no previous balance' => [
                'tbybNetSales' => Money::of(190700, 'USD'),
                'previousTotal' => Money::zero('USD'),
                'numberOfCharges' => 43,
                'totalCharges' => Money::of(7100, 'USD'),
                'netSalesTotal' => Money::of(190700, 'USD'),
                'expectedCharges' => [
                    [
                        'currency' => 'USD',
                        'amount' => 100,
                        'step_size' => 2500,
                        'step_start_amount' => 2500,
                        'step_end_amount' => 5000,
                        'store_id' => '1',
                    ],
                    [
                        'currency' => 'USD',
                        'amount' => 100,
                        'step_size' => 2500,
                        'step_start_amount' => 5000,
                        'step_end_amount' => 7500,
                        'store_id' => '1',
                    ],
                    6 => [
                        'currency' => 'USD',
                        'amount' => 100,
                        'step_size' => 2500,
                        'step_start_amount' => 17500,
                        'step_end_amount' => 20000,
                        'store_id' => '1',
                    ],
                    10 => [
                        'currency' => 'USD',
                        'amount' => 100,
                        'step_size' => 2500,
                        'step_start_amount' => 27500,
                        'step_end_amount' => 30000,
                        'store_id' => '1',
                    ],
                    39 => [
                        'currency' => 'USD',
                        'amount' => 800,
                        'step_size' => 25000,
                        'step_start_amount' => 100000,
                        'step_end_amount' => 125000,
                        'store_id' => '1',
                    ],
                    40 => [
                        'currency' => 'USD',
                        'amount' => 800,
                        'step_size' => 25000,
                        'step_start_amount' => 125000,
                        'step_end_amount' => 150000,
                        'store_id' => '1',
                    ],
                    41 => [
                        'currency' => 'USD',
                        'amount' => 800,
                        'step_size' => 25000,
                        'step_start_amount' => 150000,
                        'step_end_amount' => 175000,
                        'store_id' => '1',
                    ],
                    42 => [
                        'currency' => 'USD',
                        'amount' => 800,
                        'step_size' => 25000,
                        'step_start_amount' => 175000,
                        'step_end_amount' => 200000,
                        'store_id' => '1',
                    ],
                ],
            ],
            'multiple steps, single bracket, no refunds, with previous balance' => [
                'tbybNetSales' => Money::of(3400, 'USD'),
                'previousTotal' => Money::of(30000, 'USD'),
                'numberOfCharges' => 2,
                'totalCharges' => Money::of(200, 'USD'),
                'netSalesTotal' => Money::of(33400, 'USD'),
                'expectedCharges' => [
                    [
                        'currency' => 'USD',
                        'amount' => 100,
                        'step_size' => 2500,
                        'step_start_amount' => 30000,
                        'step_end_amount' => 32500,
                        'store_id' => '1',
                    ],
                    [
                        'currency' => 'USD',
                        'amount' => 100,
                        'step_size' => 2500,
                        'step_start_amount' => 32500,
                        'step_end_amount' => 35000,
                        'store_id' => '1',
                    ],
                ],
            ],
            'single negative step, with previous balance' => [
                'tbybNetSales' => Money::of(-2400, 'USD'),
                'previousTotal' => Money::of(15700, 'USD'),
                'numberOfCharges' => 1,
                'totalCharges' => Money::zero('USD'),
                'netSalesTotal' => Money::of(13300, 'USD'),
                'expectedCharges' => [
                    [
                        'currency' => 'USD',
                        'amount' => 0,
                        'step_size' => 0,
                        'step_start_amount' => 0,
                        'step_end_amount' => 0,
                        'store_id' => '1',
                    ],
                ],
            ],
            'multiple steps, multiple brackets, no refunds, with previous balance' => [
                'tbybNetSales' => Money::of(284000, 'USD'),
                'previousTotal' => Money::of(100000, 'USD'),
                'numberOfCharges' => 78,
                'totalCharges' => Money::of(6900, 'USD'),
                'netSalesTotal' => Money::of(384000, 'USD'),
                'expectedCharges' => [
                    [
                        'currency' => 'USD',
                        'amount' => 800,
                        'step_size' => 25000,
                        'step_start_amount' => 100000,
                        'step_end_amount' => 125000,
                        'store_id' => '1',
                    ],
                    3 => [
                        'currency' => 'USD',
                        'amount' => 800,
                        'step_size' => 25000,
                        'step_start_amount' => 175000,
                        'step_end_amount' => 200000,
                        'store_id' => '1',
                    ],
                    17 => [
                        'currency' => 'USD',
                        'amount' => 50,
                        'step_size' => 2500,
                        'step_start_amount' => 232500,
                        'step_end_amount' => 235000,
                        'store_id' => '1',
                    ],
                    31 => [
                        'currency' => 'USD',
                        'amount' => 50,
                        'step_size' => 2500,
                        'step_start_amount' => 267500,
                        'step_end_amount' => 270000,
                        'store_id' => '1',
                    ],
                    59 => [
                        'currency' => 'USD',
                        'amount' => 50,
                        'step_size' => 2500,
                        'step_start_amount' => 337500,
                        'step_end_amount' => 340000,
                        'store_id' => '1',
                    ],
                    72 => [
                        'currency' => 'USD',
                        'amount' => 50,
                        'step_size' => 2500,
                        'step_start_amount' => 370000,
                        'step_end_amount' => 372500,
                        'store_id' => '1',
                    ],
                    77 => [
                        'currency' => 'USD',
                        'amount' => 50,
                        'step_size' => 2500,
                        'step_start_amount' => 382500,
                        'step_end_amount' => 385000,
                        'store_id' => '1',
                    ],
                ],
            ],
            'multiple steps, multiple brackets, with refunds, with previous balance' => [
                'tbybNetSales' => Money::of(190700, 'USD'),
                'previousTotal' => Money::of(81560, 'USD'),
                'numberOfCharges' => 40,
                'totalCharges' => Money::of(5350, 'USD'),
                'netSalesTotal' => Money::of(272260, 'USD'),
                'expectedCharges' => [
                    [
                        'currency' => 'USD',
                        'amount' => 100,
                        'step_size' => 2500,
                        'step_start_amount' => 82500,
                        'step_end_amount' => 85000,
                        'store_id' => '1',
                    ],
                    2 => [
                        'currency' => 'USD',
                        'amount' => 100,
                        'step_size' => 2500,
                        'step_start_amount' => 87500,
                        'step_end_amount' => 90000,
                        'store_id' => '1',
                    ],
                    8 => [
                        'currency' => 'USD',
                        'amount' => 800,
                        'step_size' => 25000,
                        'step_start_amount' => 125000,
                        'step_end_amount' => 150000,
                        'store_id' => '1',
                    ],
                    10 => [
                        'currency' => 'USD',
                        'amount' => 800,
                        'step_size' => 25000,
                        'step_start_amount' => 175000,
                        'step_end_amount' => 200000,
                        'store_id' => '1',
                    ],
                    16 => [
                        'currency' => 'USD',
                        'amount' => 50,
                        'step_size' => 2500,
                        'step_start_amount' => 212500,
                        'step_end_amount' => 215000,
                        'store_id' => '1',
                    ],
                    30 => [
                        'currency' => 'USD',
                        'amount' => 50,
                        'step_size' => 2500,
                        'step_start_amount' => 247500,
                        'step_end_amount' => 250000,
                        'store_id' => '1',
                    ],
                    39 => [
                        'currency' => 'USD',
                        'amount' => 50,
                        'step_size' => 2500,
                        'step_start_amount' => 270000,
                        'step_end_amount' => 272500,
                        'store_id' => '1',
                    ],
                ],
            ],
            'multiple steps, single bracket, no refunds, with previous balance, CAD' => [
                'tbybNetSales' => Money::ofMinor(454175, 'CAD')->multipliedBy('1.345309', RoundingMode::HALF_UP),
                'previousTotal' => Money::ofMinor(203178, 'USD'),
                'numberOfCharges' => 2,
                'totalCharges' => Money::of(200, 'USD'),
                'netSalesTotal' => Money::ofMinor(657353, 'USD'),
                'expectedCharges' => [
                    [
                        'currency' => 'USD',
                        'amount' => 100,
                        'step_size' => 2500,
                        'step_start_amount' => 2500,
                        'step_end_amount' => 5000,
                        'store_id' => '1',
                    ],
                    [
                        'currency' => 'USD',
                        'amount' => 100,
                        'step_size' => 2500,
                        'step_start_amount' => 5000,
                        'step_end_amount' => 7500,
                        'store_id' => '1',
                    ],
                ],
            ],
            'under included, right under bracket, no previous balance' => [
                'tbybNetSales' => Money::of(2499.99, 'USD'),
                'previousTotal' => Money::of(0, 'USD'),
                'numberOfCharges' => 1,
                'totalCharges' => Money::zero('USD'),
                'netSalesTotal' => Money::of(2499.99, 'USD'),
                'expectedCharges' => [
                    [
                        'currency' => 'USD',
                        'amount' => 0,
                        'step_size' => 0,
                        'step_start_amount' => 0,
                        'step_end_amount' => 0,
                        'store_id' => '1',
                    ],
                ],
            ],
            'under included, at bracket, no previous balance' => [
                'tbybNetSales' => Money::of(2500, 'USD'),
                'previousTotal' => Money::of(0, 'USD'),
                'numberOfCharges' => 1,
                'totalCharges' => Money::zero('USD'),
                'netSalesTotal' => Money::of(2500, 'USD'),
                'expectedCharges' => [
                    [
                        'currency' => 'USD',
                        'amount' => 0,
                        'step_size' => 0,
                        'step_start_amount' => 0,
                        'step_end_amount' => 0,
                        'store_id' => '1',
                    ],
                ],
            ],
            'under included, 1 penny over bracket, no previous balance' => [
                'tbybNetSales' => Money::of(2500.01, 'USD'),
                'previousTotal' => Money::of(0, 'USD'),
                'numberOfCharges' => 1,
                'totalCharges' => Money::of(100, 'USD'),
                'netSalesTotal' => Money::of(2500.01, 'USD'),
                'expectedCharges' => [
                    [
                        'currency' => 'USD',
                        'amount' => 100,
                        'step_size' => 2500,
                        'step_start_amount' => 2500,
                        'step_end_amount' => 5000,
                        'store_id' => '1',
                    ],
                ],
            ],
            'right under bracket, with previous balance' => [
                'tbybNetSales' => Money::of(0, 'USD'),
                'previousTotal' => Money::of(2499.99, 'USD'),
                'numberOfCharges' => 1,
                'totalCharges' => Money::zero('USD'),
                'netSalesTotal' => Money::of(2499.99, 'USD'),
                'expectedCharges' => [
                    [
                        'currency' => 'USD',
                        'amount' => 0,
                        'step_size' => 0,
                        'step_start_amount' => 0,
                        'step_end_amount' => 0,
                        'store_id' => '1',
                    ],
                ],
            ],
            'at bracket, with previous balance' => [
                'tbybNetSales' => Money::of(0.01, 'USD'),
                'previousTotal' => Money::of(2499.99, 'USD'),
                'numberOfCharges' => 1,
                'totalCharges' => Money::zero('USD'),
                'netSalesTotal' => Money::of(2500, 'USD'),
                'expectedCharges' => [
                    [
                        'currency' => 'USD',
                        'amount' => 0,
                        'step_size' => 0,
                        'step_start_amount' => 0,
                        'step_end_amount' => 0,
                        'store_id' => '1',
                    ],
                ],
            ],
            'no sales, with previous balance' => [
                'tbybNetSales' => Money::of(0, 'USD'),
                'previousTotal' => Money::of(2500, 'USD'),
                'numberOfCharges' => 1,
                'totalCharges' => Money::of(0, 'USD'),
                'netSalesTotal' => Money::of(2500, 'USD'),
                'expectedCharges' => [
                    [
                        'currency' => 'USD',
                        'amount' => 0,
                        'step_size' => 0,
                        'step_start_amount' => 0,
                        'step_end_amount' => 0,
                        'store_id' => '1',
                    ],
                ],
            ],
            '1 penny over bracket, with previous balance' => [
                'tbybNetSales' => Money::of(0.01, 'USD'),
                'previousTotal' => Money::of(2500, 'USD'),
                'numberOfCharges' => 1,
                'totalCharges' => Money::of(100, 'USD'),
                'netSalesTotal' => Money::of(2500.01, 'USD'),
                'expectedCharges' => [
                    [
                        'currency' => 'USD',
                        'amount' => 100,
                        'step_size' => 2500,
                        'step_start_amount' => 2500,
                        'step_end_amount' => 5000,
                        'store_id' => '1',
                    ],
                ],
            ],
            '1 penny under boundary, one step' => [
                'tbybNetSales' => Money::of(4999.99, 'USD'),
                'previousTotal' => Money::zero('USD'),
                'numberOfCharges' => 1,
                'totalCharges' => Money::of(100, 'USD'),
                'netSalesTotal' => Money::of(4999.99, 'USD'),
                'expectedCharges' => [
                    [
                        'currency' => 'USD',
                        'amount' => 100,
                        'step_size' => 2500,
                        'step_start_amount' => 2500,
                        'step_end_amount' => 5000,
                        'store_id' => '1',
                    ],
                ],

            ],
            'at boundary, one step, no previous balance' => [
                'tbybNetSales' => Money::of(5000, 'USD'),
                'previousTotal' => Money::zero('USD'),
                'numberOfCharges' => 1,
                'totalCharges' => Money::of(100, 'USD'),
                'netSalesTotal' => Money::of(5000, 'USD'),
                'expectedCharges' => [
                    [
                        'currency' => 'USD',
                        'amount' => 100,
                        'step_size' => 2500,
                        'step_start_amount' => 2500,
                        'step_end_amount' => 5000,
                        'store_id' => '1',
                    ],
                ],
            ],
            'over boundary, two steps, no previous balance' => [
                'tbybNetSales' => Money::of(5000.01, 'USD'),
                'previousTotal' => Money::zero('USD'),
                'numberOfCharges' => 2,
                'totalCharges' => Money::of(200, 'USD'),
                'netSalesTotal' => Money::of(5000.01, 'USD'),
                'expectedCharges' => [
                    [
                        'currency' => 'USD',
                        'amount' => 100,
                        'step_size' => 2500,
                        'step_start_amount' => 2500,
                        'step_end_amount' => 5000,
                        'store_id' => '1',
                    ],
                    [
                        'currency' => 'USD',
                        'amount' => 100,
                        'step_size' => 2500,
                        'step_start_amount' => 5000,
                        'step_end_amount' => 7500,
                        'store_id' => '1',
                    ],
                ],
            ],
            '1 penny under boundary, with previous balance' => [
                'tbybNetSales' => Money::of(4999.99, 'USD'),
                'previousTotal' => Money::of(2500, 'USD'),
                'numberOfCharges' => 2,
                'totalCharges' => Money::of(200, 'USD'),
                'netSalesTotal' => Money::of(7499.99, 'USD'),
                'expectedCharges' => [
                    [
                        'currency' => 'USD',
                        'amount' => 100,
                        'step_size' => 2500,
                        'step_start_amount' => 2500,
                        'step_end_amount' => 5000,
                        'store_id' => '1',
                    ],
                    [
                        'currency' => 'USD',
                        'amount' => 100,
                        'step_size' => 2500,
                        'step_start_amount' => 5000,
                        'step_end_amount' => 7500,
                        'store_id' => '1',
                    ],
                ],
            ],
            'at boundary, with previous balance' => [
                'tbybNetSales' => Money::of(5000, 'USD'),
                'previousTotal' => Money::of(2500, 'USD'),
                'numberOfCharges' => 2,
                'totalCharges' => Money::of(200, 'USD'),
                'netSalesTotal' => Money::of(7500, 'USD'),
                'expectedCharges' => [
                    [
                        'currency' => 'USD',
                        'amount' => 100,
                        'step_size' => 2500,
                        'step_start_amount' => 2500,
                        'step_end_amount' => 5000,
                        'store_id' => '1',
                    ],
                    [
                        'currency' => 'USD',
                        'amount' => 100,
                        'step_size' => 2500,
                        'step_start_amount' => 5000,
                        'step_end_amount' => 7500,
                        'store_id' => '1',
                    ],
                ],
            ],
            'under boundary, with previous balance' => [
                'tbybNetSales' => Money::of(2499.99, 'USD'),
                'previousTotal' => Money::of(2500.01, 'USD'),
                'numberOfCharges' => 1,
                'totalCharges' => Money::of(0, 'USD'),
                'netSalesTotal' => Money::of(5000, 'USD'),
                'expectedCharges' => [
                    [
                        'currency' => 'USD',
                        'amount' => 0,
                        'step_size' => 2500,
                        'step_start_amount' => 0,
                        'step_end_amount' => 0,
                        'store_id' => '1',
                    ],
                ],
            ],
            'over boundary due to net sales, with previous balance' => [
                'tbybNetSales' => Money::of(5000.01, 'USD'),
                'previousTotal' => Money::of(2500, 'USD'),
                'numberOfCharges' => 3,
                'totalCharges' => Money::of(300, 'USD'),
                'netSalesTotal' => Money::of(7500.01, 'USD'),
                'expectedCharges' => [
                    [
                        'currency' => 'USD',
                        'amount' => 100,
                        'step_size' => 2500,
                        'step_start_amount' => 2500,
                        'step_end_amount' => 5000,
                        'store_id' => '1',
                    ],
                    [
                        'currency' => 'USD',
                        'amount' => 100,
                        'step_size' => 2500,
                        'step_start_amount' => 5000,
                        'step_end_amount' => 7500,
                        'store_id' => '1',
                    ],
                    [
                        'currency' => 'USD',
                        'amount' => 100,
                        'step_size' => 2500,
                        'step_start_amount' => 7500,
                        'step_end_amount' => 10000,
                        'store_id' => '1',
                    ],
                ],
            ],
            'over boundary due to previous balance' => [
                'tbybNetSales' => Money::of(2500, 'USD'),
                'previousTotal' => Money::of(2500.01, 'USD'),
                'numberOfCharges' => 1,
                'totalCharges' => Money::of(100, 'USD'),
                'netSalesTotal' => Money::of(5000.01, 'USD'),
                'expectedCharges' => [
                    [
                        'currency' => 'USD',
                        'amount' => 100,
                        'step_size' => 2500,
                        'step_start_amount' => 5000,
                        'step_end_amount' => 7500,
                        'store_id' => '1',
                    ],
                ],
            ],
        ];
    }
}
