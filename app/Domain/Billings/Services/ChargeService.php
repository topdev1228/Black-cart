<?php
declare(strict_types=1);

namespace App\Domain\Billings\Services;

use App;
use App\Domain\Billings\Events\BillableChargesCreatedEvent;
use App\Domain\Billings\Repositories\ChargeRepository;
use App\Domain\Billings\Values\Charge;
use App\Domain\Billings\Values\TbybNetSale as TbybNetSaleValue;
use App\Domain\Billings\Values\UsageConfigEntry;
use App\Domain\Shared\Facades\AppMetrics;
use Brick\Money\Money;
use Carbon\CarbonImmutable;
use DB;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\Date;
use PrinsFrank\Standards\Currency\CurrencyAlpha3;
use Throwable;

class ChargeService
{
    const EXCHANGE_RATES = [
        'ARS' => '831.465568',
        'AUD' => '1.531601',
        'BHD' => '0.376000',
        'BWP' => '13.720240',
        'BRL' => '4.953543',
        'GBP' => '0.791883',
        'BND' => '1.344754',
        'BGN' => '1.815421',
        'CAD' => '1.345309',
        'CLP' => '971.155346',
        'CNY' => '7.192921',
        'COP' => '3911.023304',
        'CZK' => '23.432696',
        'DKK' => '6.919034',
        'AED' => '3.672500',
        'EUR' => '0.928210',
        'HKD' => '7.817561',
        'HUF' => '359.124505',
        'ISK' => '137.644768',
        'INR' => '82.997970',
        'IDR' => '15574.982215',
        'IRR' => '41885.413052',
        'ILS' => '3.659445',
        'JPY' => '149.331125',
        'KZT' => '448.219039',
        'KWD' => '0.307895',
        'LYD' => '4.838877',
        'MYR' => '4.764712',
        'MUR' => '45.342406',
        'MXN' => '17.079825',
        'NPR' => '132.859001',
        'NZD' => '1.631043',
        'NOK' => '10.501871',
        'OMR' => '0.385001',
        'PKR' => '279.497560',
        'PHP' => '56.007479',
        'PLN' => '4.003394',
        'QAR' => '3.640000',
        'RON' => '4.618371',
        'RUB' => '91.089690',
        'SAR' => '3.750000',
        'SGD' => '1.344754',
        'ZAR' => '18.965463',
        'KRW' => '1329.192826',
        'LKR' => '313.132296',
        'SEK' => '10.408573',
        'CHF' => '0.875707',
        'TWD' => '31.359088',
        'THB' => '35.907519',
        'TTD' => '6.779743',
        'TRY' => '30.723515',
        'VEF' => '3626586.059539',
    ];
    const SHOPIFY_MONTHLY_RECURRING_BILLING_PERIOD_DAYS = 30;

    public function __construct(protected ChargeRepository $chargeRepository, protected UsageConfigService $usageConfigService, protected ShopifySubscriptionService $shopifySubscriptionService, protected SubscriptionService $subscriptionService)
    {
    }

    public function createCharges(TbybNetSaleValue $tbybNetSaleValue): Collection
    {
        $tbybNetSales = $tbybNetSaleValue->tbybNetSales;

        if ($tbybNetSales->getCurrency()->getCurrencyCode() !== CurrencyAlpha3::US_Dollar->value) {
            $tbybNetSales = Money::ofMinor(
                $tbybNetSales->dividedBy(
                    self::EXCHANGE_RATES[$tbybNetSales->getCurrency()->getCurrencyCode()],
                    config('money.rounding')
                )->getMinorAmount()->toInt(),
                CurrencyAlpha3::US_Dollar->value
            );
        }

        $previousTotal = Money::zero($tbybNetSales->getCurrency()->getCurrencyCode());
        if (!$tbybNetSaleValue->isFirstOfBillingPeriod) {
            $lastCharge = $this->chargeRepository->getPriorByTimeRangeEnd($tbybNetSaleValue->timeRangeEnd);
            if ($lastCharge !== null) {
                $previousTotal = $lastCharge->balance;
            }
        }

        $result = $this->calculateCharges(
            $tbybNetSaleValue->id,
            $tbybNetSales,
            $previousTotal,
            $tbybNetSaleValue->timeRangeStart,
            $tbybNetSaleValue->timeRangeEnd,
            $tbybNetSaleValue->isFirstOfBillingPeriod,
        );
        AppMetrics::setTag('billing.charges.count', $result['charges']->count());
        AppMetrics::setTag('billing.charges.net_sales_total', $result['net_sales_total']->formatTo('en_US', true));
        $result['charges']->each(function (Charge $charge) {
            $this->chargeRepository->create($charge);
        });

        BillableChargesCreatedEvent::dispatch();

        return $result;
    }

    protected function calculateCharges(
        string $tbybNetSaleId,
        Money $tbybNetSales,
        Money $previousTotal,
        CarbonImmutable $timeRangeStart,
        CarbonImmutable $timeRangeEnd,
        bool $isFirstOfBillingPeriod = false,
    ): Collection {
        $config = $this->usageConfigService->getLatestConfig();

        // Initial subscription fee
        $included = $config->config->filter(function (UsageConfigEntry $step) {
            return $step->start->isZero() && $step->price->isZero();
        })->first()?->end;

        $result = collect(['net_sales_total' => $previousTotal->plus($tbybNetSales), 'charges' => Collection::empty()]);

        if (($included !== null && $result['net_sales_total']->isLessThanOrEqualTo($included)) || $result['net_sales_total']->isLessThanOrEqualTo($previousTotal)) {
            // No charges will be made
            $result['charges']->add(Charge::from([
                'tbyb_net_sale_id' => $tbybNetSaleId,
                'currency' => $config->currency->value,
                'amount' => Money::zero($config->currency->value),
                'balance' => $result['net_sales_total'],
                'is_billed' => false,
                'time_range_start' => $timeRangeStart,
                'time_range_end' => $timeRangeEnd,
                'step_size' => Money::zero($config->currency->value),
                'step_start_amount' => Money::zero($config->currency->value),
                'step_end_amount' => Money::zero($config->currency->value),
                'store_id' => App::context()->store->id,
                'is_first_of_billing_period' => $isFirstOfBillingPeriod,
            ]));

            return $result;
        }

        // Find the amount left in the current Step
        $currentTotal = Money::ofMinor(1, $config->currency->value);
        while ($currentTotal->isLessThanOrEqualTo($previousTotal)) {
            /** @var UsageConfigEntry $step */
            $step = $config->config->toCollection()->sortBy(function (UsageConfigEntry $step) {
                // Sort by the start amount
                return $step->start->getMinorAmount()->toInt();
            })->filter(function (UsageConfigEntry $step) use ($currentTotal) {
                // Get the first  (ordered) step with an end greater than the current total
                return $step->end?->isGreaterThan($currentTotal) || $step->end === null;
            })->first();

            $currentTotal = $currentTotal->plus($step->step, config('money.rounding'));
        }

        // Amount left in the current step
        $partialStep = $currentTotal->minus($previousTotal)->minus(Money::ofMinor(1, $config->currency->value));

        // If the partial step is greater than the net sales, then no charges will be made because we won't cross a step-boundary
        if (isset($step) && $partialStep->isGreaterThanOrEqualTo($tbybNetSales)) {
            $result['charges']->add(Charge::from([
                'tbyb_net_sale_id' => $tbybNetSaleId,
                'currency' => $config->currency->value,
                'amount' => Money::zero($config->currency->value),
                'balance' => $result['net_sales_total'],
                'is_billed' => false,
                'time_range_start' => $timeRangeStart,
                'time_range_end' => $timeRangeEnd,
                'step_size' => $step->step,
                'step_start_amount' => Money::zero($config->currency->value),
                'step_end_amount' => Money::zero($config->currency->value),
                'store_id' => App::context()->store->id,
                'is_first_of_billing_period' => $isFirstOfBillingPeriod,
            ]));

            return $result;
        }

        // Reset current step to the beginning of step
        if (isset($step) && $partialStep->isPositive()) {
            $currentTotal = clone $previousTotal->plus($partialStep, config('money.rounding'))->plus(Money::ofMinor(1, $config->currency->value), config('money.rounding'));
        } else {
            $currentTotal = clone $previousTotal->plus(Money::ofMinor(1, $config->currency->value));
        }
        while (!$currentTotal->isGreaterThan($result['net_sales_total'])) {
            /** @var UsageConfigEntry $step */
            $step = $config->config->toCollection()->sortBy(function (UsageConfigEntry $step) {
                // Sort by the start amount
                return $step->start->getMinorAmount()->toInt();
            })->filter(function (UsageConfigEntry $step) use ($currentTotal) {
                // Get the first  (ordered) step with an end greater than the current total
                return $step->end?->isGreaterThan($currentTotal) || $step->end === null;
            })->first();

            $currentTotal = $currentTotal->plus($step->step, config('money.rounding'));
            if ($step->price->isPositive()) {
                // Ignore any $0 steps
                $result['charges']->add(Charge::from([
                    'tbyb_net_sale_id' => $tbybNetSaleId,
                    'currency' => $config->currency->value,
                    'amount' => $step->price,
                    'balance' => $result['net_sales_total'],
                    'is_billed' => false,
                    'time_range_start' => $timeRangeStart,
                    'time_range_end' => $timeRangeEnd,
                    'step_size' => $step->step,
                    'step_start_amount' => $currentTotal->minus($step->step)->minus(Money::ofMinor(1, $config->currency->value)),
                    'step_end_amount' => $currentTotal->minus(Money::ofMinor(1, $config->currency->value)),
                    'store_id' => App::context()->store->id,
                    'is_first_of_billing_period' => $isFirstOfBillingPeriod,
                ]));
            }
        }

        return $result;
    }

    /**
     * @return Collection<Charge>
     * @throws Throwable
     */
    public function billCharges(): Collection
    {
        return DB::transaction(function () {
            $charges = $this->chargeRepository->summarizeCharges();
            $lineItems = Collection::empty();
            $usageConfig = $this->usageConfigService->getLatestConfig();
            $subscription = $this->subscriptionService->getActiveSubscription();

            $isTrial = $subscription->trialPeriodEnd !== null && Date::now()->lessThanOrEqualTo($subscription->trialPeriodEnd);

            AppMetrics::setTag('billings.summary.count', $charges['summary']->count());
            $charges['summary']->each(function (Charge $charge) use (&$lineItems, $usageConfig, $isTrial) {
                if ($charge->amount->isZero()) {
                    AppMetrics::setTag('billings.summary.has_zero_amount', '1');

                    return;
                }

                AppMetrics::setTag('billings.summary.has_non_zero_amount', '1');

                /** @var Money $amount */
                $description = sprintf(
                    '%s in TBYB net sales @ %s x %s (%s - %s)',
                    $charge->stepSize->formatTo('en_US', true),
                    $charge->amount->formatTo('en_US', true),
                    $charge->quantity,
                    $charge->stepStartAmount->formatTo('en_US', true),
                    $charge->stepEndAmount->formatTo('en_US', true)
                );

                if ($isTrial) {
                    $description .= ' (Trial)';
                    $charge->total = Money::zero($charge->total->getCurrency());
                }

                if ($charge->total->isPositive()) {
                    $this->shopifySubscriptionService->addUsage($description, $charge->total, $usageConfig);
                }

                $charge->description = $description;
                $lineItems = $lineItems->add($charge);
            });

            $charges['charges']->each(function (Charge $charge) {
                $this->chargeRepository->markChargeAsBilled($charge);
            });

            return $lineItems;
        });
    }
}
