<?php
declare(strict_types=1);

namespace Tests\Unit\Domain\Billings\Repositories;

use App;
use App\Domain\Billings\Models\Charge;
use App\Domain\Billings\Repositories\ChargeRepository;
use App\Domain\Billings\Values\Charge as ChargeValue;
use App\Domain\Stores\Models\Store;
use Brick\Math\RoundingMode;
use Carbon\CarbonImmutable;
use Illuminate\Database\Eloquent\Factories\Sequence;
use Illuminate\Support\Facades\Date;
use PHPUnit\Framework\Attributes\DataProvider;
use PrinsFrank\Standards\Currency\CurrencyAlpha3;
use Tests\TestCase;

class ChargeRepositoryTest extends TestCase
{
    public function testItGetsPriorChargeByTimeRangeEnd(): void
    {
        $store = Store::factory()->create();
        App::context(store: $store);

        Charge::factory(['store_id' => $store->id])->count(3)->state(new Sequence(
            ['time_range_end' => Date::now()->subDays(3), 'amount' => 1, 'balance' => 1],
            ['time_range_end' => Date::now()->subDays(2), 'amount' => 2, 'balance' => 2],
            ['time_range_end' => Date::now()->subSecond(), 'amount' => 3, 'balance' => 3]
        ))->create();

        $chargeRepository = resolve(ChargeRepository::class);

        $priorCharge = $chargeRepository->getPriorByTimeRangeEnd(CarbonImmutable::now());
        $this->assertEquals(3, $priorCharge->amount->getMinorAmount()->toInt());
        $this->assertEquals(3, $priorCharge->balance->getMinorAmount()->toInt());
    }

    public function testItGetsPriorChargeByTimeRangeEndMiddle(): void
    {
        $store = Store::factory()->create();
        App::context(store: $store);

        Charge::factory(['store_id' => $store->id])->count(3)->state(new Sequence(
            ['time_range_end' => Date::now()->subDays(3), 'amount' => 1, 'balance' => 1],
            ['time_range_end' => Date::now()->subDays(2), 'amount' => 2, 'balance' => 2],
            ['time_range_end' => Date::now(), 'amount' => 3, 'balance' => 3]
        ))->create();

        $chargeRepository = resolve(ChargeRepository::class);

        $priorCharge = $chargeRepository->getPriorByTimeRangeEnd(CarbonImmutable::now()->subDays(2)->addSecond());
        $this->assertEquals(2, $priorCharge->amount->getMinorAmount()->toInt());
        $this->assertEquals(2, $priorCharge->balance->getMinorAmount()->toInt());
    }

    public function testItGetsNoPriorChargeByTimeRangeEnd(): void
    {
        $store = Store::factory()->create();
        App::context(store: $store);

        $chargeRepository = resolve(ChargeRepository::class);

        $priorCharge = $chargeRepository->getPriorByTimeRangeEnd(CarbonImmutable::now());
        $this->assertNull($priorCharge);
    }

    public function testItCreatesCharges(): void
    {
        $store = Store::factory()->create();
        App::context(store: $store);

        $chargeRepository = resolve(ChargeRepository::class);

        $charge = $chargeRepository->create(
            ChargeValue::builder()->create([
                'amount' => 10000,
                'balance' => 10000,
                'currency' => 'USD',
                'isBilled' => false,
                'store_id' => 1,
            ])
        );
        $this->assertEquals(100, $charge->amount->getAmount()->toInt());
        $this->assertEquals(100, $charge->balance->getAmount()->toInt());
        $this->assertFalse($charge->isBilled);
    }

    public function testItMarksChargeAsBilled(): void
    {
        CarbonImmutable::setTestNow('2024-05-01 00:00:00 UTC');

        $store = Store::factory()->create();
        App::context(store: $store);

        $charge = Charge::factory(['store_id' => $store->id, 'is_billed' => false])->create();

        $chargeRepository = resolve(ChargeRepository::class);

        $this->assertFalse($charge->is_billed);
        $charge = $chargeRepository->markChargeAsBilled(ChargeValue::from($charge->toArray()));
        $this->assertTrue($charge->isBilled);
        $this->assertEquals(CarbonImmutable::now(), $charge->billedAt);

        $this->assertDatabaseHas('billings_charges', [
            'id' => $charge->id,
            'is_billed' => true,
            'billed_at' => CarbonImmutable::now(),
        ]);
    }

    #[DataProvider('chargesProvider')]
    public function testItSummarizesCharges($charges, $total_charges, $total_charge_amount, $summarized_charges): void
    {
        $store = Store::factory()->create();
        App::context(store: $store);

        foreach ($charges as $charge) {
            Charge::factory(
                [
                    'store_id' => $store->id,
                    'is_billed' => $charge['is_billed'],
                    'amount' => $charge['amount'],
                    'currency' => $charge['currency'],
                    'step_size' => $charge['step_size'],
                    'step_start_amount' => $charge['step_start_amount'],
                    'step_end_amount' => $charge['step_end_amount'],
                ]
            )->create();
        }

        $chargeRepository = resolve(ChargeRepository::class);

        $summary = $chargeRepository->summarizeCharges();
        $this->assertCount(2, $summary);
        $this->assertCount($total_charges, $summary['charges']);
        $this->assertCount(count($summarized_charges), $summary['summary']);

        foreach ($summary['summary'] as $key => $charge) {
            /** @var ChargeValue $charge */
            $this->assertEquals($charge->currency, $summarized_charges[$key]['currency']);
            $this->assertEquals($charge->quantity, $summarized_charges[$key]['quantity']);
            $this->assertEquals($charge->amount->getMinorAmount()->toInt(), $summarized_charges[$key]['amount']);
            $this->assertEquals($charge->stepSize->getMinorAmount()->toInt(), $summarized_charges[$key]['step_size']);
            $this->assertEquals($charge->stepStartAmount->getMinorAmount()->toInt(), $summarized_charges[$key]['step_start_amount']);
            $this->assertEquals($charge->stepEndAmount->getMinorAmount()->toInt(), $summarized_charges[$key]['step_end_amount']);
        }

        if ($total_charge_amount !== null) {
            $totalAmount = collect($summary['summary'])->reduce(function (int $carry, array $charge) {
                return $carry + $charge['amount']->multipliedBy($charge['quantity'], RoundingMode::HALF_UP)->getMinorAmount()->toInt();
            }, 0);

            $this->assertEquals($total_charge_amount, $totalAmount);
        }
    }

    public static function chargesProvider()
    {
        return [
            'no charges' => [
                'charges' => [],
                'total_charges' => 0,
                'total_charge_amount' => null,
                'summarized_charges' => [],
            ],
            'no unbilled charges' => [
                'charges' => [
                    [
                        'is_billed' => true,
                        'amount' => 10000,
                        'currency' => CurrencyAlpha3::US_Dollar,
                        'step_size' => 250000,
                        'step_start_amount' => 0,
                        'step_end_amount' => 250000,
                    ],
                    [
                        'is_billed' => true,
                        'amount' => 20000,
                        'currency' => CurrencyAlpha3::US_Dollar,
                        'step_size' => 250000,
                        'step_start_amount' => 250001,
                        'step_end_amount' => 500000,
                    ],
                ],
                'total_charges' => 0,
                'total_charge_amount' => null,
                'summarized_charges' => [],
            ],
            'single bracket, is_billed = false' => [
                'charges' => [
                    [
                        'is_billed' => false,
                        'amount' => 10000,
                        'currency' => CurrencyAlpha3::US_Dollar,
                        'step_size' => 250000,
                        'step_start_amount' => 0,
                        'step_end_amount' => 250000,
                    ],
                    [
                        'is_billed' => false,
                        'amount' => 10000,
                        'currency' => CurrencyAlpha3::US_Dollar,
                        'step_size' => 250000,
                        'step_start_amount' => 250001,
                        'step_end_amount' => 500000,
                    ],
                    [
                        'is_billed' => false,
                        'amount' => 10000,
                        'currency' => CurrencyAlpha3::US_Dollar,
                        'step_size' => 250000,
                        'step_start_amount' => 500001,
                        'step_end_amount' => 750000,
                    ],
                ],
                'total_charges' => 3,
                'total_charge_amount' => 30000,
                'summarized_charges' => [
                    [
                        'is_billed' => false,
                        'amount' => 10000,
                        'currency' => CurrencyAlpha3::US_Dollar,
                        'step_size' => 250000,
                        'step_start_amount' => 0,
                        'step_end_amount' => 750000,
                        'quantity' => 3,
                    ],
                ],
            ],
            'multiple brackets, is_billed = false' => [
                'charges' => [
                    [
                        'is_billed' => false,
                        'amount' => 10000,
                        'currency' => CurrencyAlpha3::US_Dollar,
                        'step_size' => 250000,
                        'step_start_amount' => 0,
                        'step_end_amount' => 250000,
                    ],
                    [
                        'is_billed' => false,
                        'amount' => 10000,
                        'currency' => CurrencyAlpha3::US_Dollar,
                        'step_size' => 250000,
                        'step_start_amount' => 250001,
                        'step_end_amount' => 500000,
                    ],
                    [
                        'is_billed' => false,
                        'amount' => 10000,
                        'currency' => CurrencyAlpha3::US_Dollar,
                        'step_size' => 250000,
                        'step_start_amount' => 500001,
                        'step_end_amount' => 750000,
                    ],
                    [
                        'is_billed' => false,
                        'amount' => 20000,
                        'currency' => CurrencyAlpha3::US_Dollar,
                        'step_size' => 2500000,
                        'step_start_amount' => 750001,
                        'step_end_amount' => 3250000,
                    ],
                    [
                        'is_billed' => false,
                        'amount' => 20000,
                        'currency' => CurrencyAlpha3::US_Dollar,
                        'step_size' => 2500000,
                        'step_start_amount' => 3250001,
                        'step_end_amount' => 5750000,
                    ],
                ],
                'total_charges' => 5,
                'total_charge_amount' => 70000,
                'summarized_charges' => [
                    [
                        'is_billed' => false,
                        'amount' => 10000,
                        'currency' => CurrencyAlpha3::US_Dollar,
                        'step_size' => 250000,
                        'step_start_amount' => 0,
                        'step_end_amount' => 750000,
                        'quantity' => 3,
                    ],
                    [
                        'is_billed' => false,
                        'amount' => 20000,
                        'currency' => CurrencyAlpha3::US_Dollar,
                        'step_size' => 2500000,
                        'step_start_amount' => 750001,
                        'step_end_amount' => 5750000,
                        'quantity' => 2,
                    ],
                ],
            ],
            'single bracket, is_billed = mixed' => [
                'charges' => [
                    [
                        'is_billed' => true,
                        'amount' => 10000,
                        'currency' => CurrencyAlpha3::US_Dollar,
                        'step_size' => 250000,
                        'step_start_amount' => 0,
                        'step_end_amount' => 250000,
                    ],
                    [
                        'is_billed' => false,
                        'amount' => 10000,
                        'currency' => CurrencyAlpha3::US_Dollar,
                        'step_size' => 250000,
                        'step_start_amount' => 250001,
                        'step_end_amount' => 500000,
                    ],
                    [
                        'is_billed' => false,
                        'amount' => 10000,
                        'currency' => CurrencyAlpha3::US_Dollar,
                        'step_size' => 250000,
                        'step_start_amount' => 500001,
                        'step_end_amount' => 750000,
                    ],
                ],
                'total_charges' => 2,
                'total_charge_amount' => 20000,
                'summarized_charges' => [
                    [
                        'is_billed' => false,
                        'amount' => 10000,
                        'currency' => CurrencyAlpha3::US_Dollar,
                        'step_size' => 250000,
                        'step_start_amount' => 250001,
                        'step_end_amount' => 750000,
                        'quantity' => 2,
                    ],
                ],
            ],
            'multiple brackets, is_billed = mixed' => [
                'charges' => [
                    [
                        'is_billed' => true,
                        'amount' => 10000,
                        'currency' => CurrencyAlpha3::US_Dollar,
                        'step_size' => 250000,
                        'step_start_amount' => 0,
                        'step_end_amount' => 250000,
                    ],
                    [
                        'is_billed' => true,
                        'amount' => 10000,
                        'currency' => CurrencyAlpha3::US_Dollar,
                        'step_size' => 250000,
                        'step_start_amount' => 250001,
                        'step_end_amount' => 500000,
                    ],
                    [
                        'is_billed' => false,
                        'amount' => 10000,
                        'currency' => CurrencyAlpha3::US_Dollar,
                        'step_size' => 250000,
                        'step_start_amount' => 500001,
                        'step_end_amount' => 750000,
                    ],
                    [
                        'is_billed' => false,
                        'amount' => 20000,
                        'currency' => CurrencyAlpha3::US_Dollar,
                        'step_size' => 2500000,
                        'step_start_amount' => 750001,
                        'step_end_amount' => 3250000,
                    ],
                    [
                        'is_billed' => false,
                        'amount' => 20000,
                        'currency' => CurrencyAlpha3::US_Dollar,
                        'step_size' => 2500000,
                        'step_start_amount' => 3250001,
                        'step_end_amount' => 5750000,
                    ],
                    [
                        'is_billed' => false,
                        'amount' => 20000,
                        'currency' => CurrencyAlpha3::US_Dollar,
                        'step_size' => 2500000,
                        'step_start_amount' => 5750001,
                        'step_end_amount' => 8250000,
                    ],
                ],
                'total_charges' => 4,
                'total_charge_amount' => 70000,
                'summarized_charges' => [
                    [
                        'is_billed' => false,
                        'amount' => 10000,
                        'currency' => CurrencyAlpha3::US_Dollar,
                        'step_size' => 250000,
                        'step_start_amount' => 500001,
                        'step_end_amount' => 750000,
                        'quantity' => 1,
                    ],
                    [
                        'is_billed' => false,
                        'amount' => 20000,
                        'currency' => CurrencyAlpha3::US_Dollar,
                        'step_size' => 2500000,
                        'step_start_amount' => 750001,
                        'step_end_amount' => 8250000,
                        'quantity' => 3,
                    ],
                ],
            ],
            'multiple brackets, same step size, different amount' => [
                'charges' => [
                    [
                        'is_billed' => true,
                        'amount' => 10000,
                        'currency' => CurrencyAlpha3::US_Dollar,
                        'step_size' => 250000,
                        'step_start_amount' => 0,
                        'step_end_amount' => 250000,
                    ],
                    [
                        'is_billed' => true,
                        'amount' => 10000,
                        'currency' => CurrencyAlpha3::US_Dollar,
                        'step_size' => 250000,
                        'step_start_amount' => 250001,
                        'step_end_amount' => 500000,
                    ],
                    [
                        'is_billed' => false,
                        'amount' => 10000,
                        'currency' => CurrencyAlpha3::US_Dollar,
                        'step_size' => 250000,
                        'step_start_amount' => 500001,
                        'step_end_amount' => 750000,
                    ],
                    [
                        'is_billed' => false,
                        'amount' => 8000,
                        'currency' => CurrencyAlpha3::US_Dollar,
                        'step_size' => 250000,
                        'step_start_amount' => 750001,
                        'step_end_amount' => 1000000,
                    ],
                    [
                        'is_billed' => false,
                        'amount' => 8000,
                        'currency' => CurrencyAlpha3::US_Dollar,
                        'step_size' => 250000,
                        'step_start_amount' => 1000001,
                        'step_end_amount' => 1250000,
                    ],
                    [
                        'is_billed' => false,
                        'amount' => 8000,
                        'currency' => CurrencyAlpha3::US_Dollar,
                        'step_size' => 250000,
                        'step_start_amount' => 1250001,
                        'step_end_amount' => 1500000,
                    ],
                ],
                'total_charges' => 4,
                'total_charge_amount' => 34000,
                'summarized_charges' => [
                    [
                        'is_billed' => false,
                        'amount' => 10000,
                        'currency' => CurrencyAlpha3::US_Dollar,
                        'step_size' => 250000,
                        'step_start_amount' => 500001,
                        'step_end_amount' => 750000,
                        'quantity' => 1,
                    ],
                    [
                        'is_billed' => false,
                        'amount' => 8000,
                        'currency' => CurrencyAlpha3::US_Dollar,
                        'step_size' => 250000,
                        'step_start_amount' => 750001,
                        'step_end_amount' => 1500000,
                        'quantity' => 3,
                    ],
                ],
            ],
            'multiple brackets, different step size, same amount' => [
                'charges' => [
                    [
                        'is_billed' => true,
                        'amount' => 10000,
                        'currency' => CurrencyAlpha3::US_Dollar,
                        'step_size' => 250000,
                        'step_start_amount' => 0,
                        'step_end_amount' => 250000,
                    ],
                    [
                        'is_billed' => true,
                        'amount' => 10000,
                        'currency' => CurrencyAlpha3::US_Dollar,
                        'step_size' => 250000,
                        'step_start_amount' => 250001,
                        'step_end_amount' => 500000,
                    ],
                    [
                        'is_billed' => false,
                        'amount' => 10000,
                        'currency' => CurrencyAlpha3::US_Dollar,
                        'step_size' => 250000,
                        'step_start_amount' => 500001,
                        'step_end_amount' => 750000,
                    ],
                    [
                        'is_billed' => false,
                        'amount' => 10000,
                        'currency' => CurrencyAlpha3::US_Dollar,
                        'step_size' => 2500000,
                        'step_start_amount' => 750001,
                        'step_end_amount' => 3250000,
                    ],
                    [
                        'is_billed' => false,
                        'amount' => 10000,
                        'currency' => CurrencyAlpha3::US_Dollar,
                        'step_size' => 2500000,
                        'step_start_amount' => 3250001,
                        'step_end_amount' => 5750000,
                    ],
                    [
                        'is_billed' => false,
                        'amount' => 10000,
                        'currency' => CurrencyAlpha3::US_Dollar,
                        'step_size' => 2500000,
                        'step_start_amount' => 5750001,
                        'step_end_amount' => 8250000,
                    ],
                ],
                'total_charges' => 4,
                'total_charge_amount' => 40000,
                'summarized_charges' => [
                    [
                        'is_billed' => false,
                        'amount' => 10000,
                        'currency' => CurrencyAlpha3::US_Dollar,
                        'step_size' => 250000,
                        'step_start_amount' => 500001,
                        'step_end_amount' => 750000,
                        'quantity' => 1,
                    ],
                    [
                        'is_billed' => false,
                        'amount' => 10000,
                        'currency' => CurrencyAlpha3::US_Dollar,
                        'step_size' => 2500000,
                        'step_start_amount' => 750001,
                        'step_end_amount' => 8250000,
                        'quantity' => 3,
                    ],
                ],
            ],
        ];
    }
}
