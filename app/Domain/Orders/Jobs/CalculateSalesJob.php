<?php
declare(strict_types=1);

namespace App\Domain\Orders\Jobs;

use App\Domain\Orders\Services\OrderService;
use App\Domain\Orders\Services\RefundService;
use App\Domain\Orders\Services\TbybNetSaleService;
use App\Domain\Orders\Values\Subscription as SubscriptionValue;
use App\Domain\Orders\Values\TbybNetSale as TbybNetSaleValue;
use App\Domain\Shared\Jobs\BaseJob;
use Brick\Money\Money;
use Carbon\CarbonImmutable;
use Exception;
use Http;
use Illuminate\Database\Eloquent\ModelNotFoundException;
use Illuminate\Foundation\Bus\PendingDispatch;
use Log;
use PrinsFrank\Standards\Currency\CurrencyAlpha3;

/**
 * @method static PendingDispatch dispatch(string $storeId)
 * @method static PendingDispatch dispatchSync(string $storeId)
 */
class CalculateSalesJob extends BaseJob
{
    const SHOPIFY_MONTHLY_RECURRING_BILLING_PERIOD_DAYS = 30;

    public function __construct(protected string $storeId)
    {
        parent::__construct();
    }

    public function handle(OrderService $orderService, RefundService $refundService, TbybNetSaleService $tbybNetSaleService): void
    {
        Log::info('In calculate sales job', ['store_id' => $this->storeId]);

        $activeSubscription = $this->getActiveSubscription();
        if ($activeSubscription === null) {
            Log::info('No active subscription found', ['store_id' => $this->storeId]);

            return;
        }

        $startDate = $activeSubscription->activatedAt;
        $endDate = CarbonImmutable::now();
        $isFirstCalculation = false;

        try {
            $latestTbybNetSale = $tbybNetSaleService->getLatest();
            $startDate = $latestTbybNetSale->timeRangeEnd;
        } catch (ModelNotFoundException) {
            Log::info('TbybNetSale entry not found. Using subscription activated date as the start date', ['store_id' => $this->storeId]);
            $isFirstCalculation = true;
        } catch (Exception $e) {
            throw $e;
        }

        /*
         * Find all billing period boundaries between the start and end date
         */
        $daysSinceSubscriptionActivation = intval($activeSubscription->activatedAt->diffInUTCDays($startDate));

        $isFirstOfBillingPeriod = $isFirstCalculation ||
            (($activeSubscription->activatedAt->diffInUTCSeconds($startDate)) % (static::SHOPIFY_MONTHLY_RECURRING_BILLING_PERIOD_DAYS * 60 * 60 * 24) === 0);

        // The integer division will either land us right at the end of the billing period or the billing period before the start date.
        // Adding 1 in the end will make sure the billing period end is after the start date.
        $billingPeriodsFromActivationToStartDate = intval($daysSinceSubscriptionActivation / static::SHOPIFY_MONTHLY_RECURRING_BILLING_PERIOD_DAYS) + 1;

        $nextBillingPeriodEndDate = $activeSubscription->activatedAt->addDays($billingPeriodsFromActivationToStartDate * static::SHOPIFY_MONTHLY_RECURRING_BILLING_PERIOD_DAYS);
        if ($nextBillingPeriodEndDate->isAfter($endDate)) {
            // Start and end date within the same billing period
            $this->calculateTbybNetSales($startDate, $endDate, $isFirstOfBillingPeriod, $orderService, $refundService, $tbybNetSaleService);

            return;
        }

        $interimEndDates = [$nextBillingPeriodEndDate];
        while ($nextBillingPeriodEndDate->isBefore($endDate)) {
            $nextBillingPeriodEndDate = $nextBillingPeriodEndDate->addDays(static::SHOPIFY_MONTHLY_RECURRING_BILLING_PERIOD_DAYS);
            if ($nextBillingPeriodEndDate->isAfter($endDate)) {
                $nextBillingPeriodEndDate = $endDate;
            }
            $interimEndDates[] = $nextBillingPeriodEndDate;
        }

        $interimStartDate = $startDate;
        foreach ($interimEndDates as $interimEndDate) {
            $this->calculateTbybNetSales($interimStartDate, $interimEndDate, $isFirstOfBillingPeriod, $orderService, $refundService, $tbybNetSaleService);
            $interimStartDate = $interimEndDate;
            $isFirstOfBillingPeriod = true;
        }
    }

    protected function calculateTbybNetSales(
        CarbonImmutable $startDate,
        CarbonImmutable $endDate,
        bool $isFirstOfBillingPeriod,
        OrderService $orderService,
        RefundService $refundService,
        TbybNetSaleService $tbybNetSaleService,
    ): void {
        try {
            $storeCurrency = $orderService->getShopCurrency();
        } catch (ModelNotFoundException) {
            // Shop currency not found. Using default currency.
            $storeCurrency = CurrencyAlpha3::US_Dollar;
        } catch (Exception $e) {
            throw $e;
        }

        $orderGrossSalesString = $orderService->getGrossSales($endDate, $startDate);
        $orderGrossSales = Money::ofMinor($orderGrossSalesString, $storeCurrency->value);
        $orderDiscountsString = $orderService->getTotalDiscounts($endDate, $startDate);
        $orderDiscounts = Money::ofMinor($orderDiscountsString, $storeCurrency->value);

        $refundGrossSalesString = $refundService->getGrossSales($endDate, $startDate);
        $refundGrossSales = Money::ofMinor($refundGrossSalesString, $storeCurrency->value);
        $refundDiscountsString = $refundService->getDiscounts($endDate, $startDate);
        $refundDiscounts = Money::ofMinor($refundDiscountsString, $storeCurrency->value);

        $orderNetSales = $orderGrossSales->minus($orderDiscounts);
        $refundNetSales = $refundGrossSales->minus($refundDiscounts);
        $newNetSales = $orderNetSales->minus($refundNetSales);

        $tbybNetSale = TbybNetSaleValue::from([
            'store_id' => $this->storeId,
            'date_start' => $startDate,
            'date_end' => $endDate,
            'time_range_start' => $startDate,
            'time_range_end' => $endDate,
            'currency' => $storeCurrency,
            'tbyb_gross_sales' => $orderGrossSales,
            'tbyb_discounts' => $orderDiscounts,
            'tbyb_refunded_gross_sales' => $refundGrossSales,
            'tbyb_refunded_discounts' => $refundDiscounts,
            'tbyb_net_sales' => $newNetSales,
            'is_first_of_billing_period' => $isFirstOfBillingPeriod,
        ]);

        $tbybNetSaleService->create($tbybNetSale);
    }

    protected function getActiveSubscription(): ?SubscriptionValue
    {
        $response = Http::get('http://localhost:8080/api/stores/billings/subscriptions/active');
        $subscriptionData = $response['subscription'] ?? null;

        return $subscriptionData ? SubscriptionValue::from($subscriptionData) : null;
    }
}
