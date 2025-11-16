<?php
declare(strict_types=1);

namespace App\Domain\Billings\Repositories;

use App;
use App\Domain\Billings\Models\Charge;
use App\Domain\Billings\Values\Charge as ChargeValue;
use Carbon\CarbonImmutable;
use DB;
use Illuminate\Database\Eloquent\ModelNotFoundException;
use Illuminate\Database\Query\JoinClause;
use Illuminate\Support\Collection;

class ChargeRepository
{
    public function getPriorByTimeRangeEnd(CarbonImmutable $timeRangeEnd): ?ChargeValue
    {
        try {
            return ChargeValue::from(
                Charge::where('time_range_end', '<', $timeRangeEnd)
                    ->orderBy('time_range_end', 'desc')
                    ->firstOrFail()
            );
        } catch (ModelNotFoundException) {
            return null;
        }
    }

    public function create(ChargeValue $charge): ChargeValue
    {
        return ChargeValue::from(Charge::create($charge->toArray()));
    }

    public function markChargeAsBilled(ChargeValue $charge): ChargeValue
    {
        $billedAt = CarbonImmutable::now('UTC');
        if (Charge::where('id', $charge->id)->update(['is_billed' => true, 'billed_at' => $billedAt])) {
            $charge->isBilled = true;
            $charge->billedAt = CarbonImmutable::now('UTC');
        }

        return $charge;
    }

    public function summarizeCharges(): Collection
    {
        return DB::transaction(function () {
            $minSteps = DB::table('billings_charges')
                ->select('amount', 'step_size', DB::raw('MIN(step_start_amount) as step_start_amount'))
                ->where('store_id', App::context()->store->id)
                ->where('is_billed', false)
                ->groupBy('amount', 'step_size');

            $maxSteps = DB::table('billings_charges')
                ->select('amount', 'step_size', DB::raw('MAX(step_end_amount) as step_end_amount'))
                ->where('store_id', App::context()->store->id)
                ->where('is_billed', false)
                ->groupBy('amount', 'step_size');

            $summary = DB::table('billings_charges')
                ->select([
                    'billings_charges.amount',
                    DB::raw('billings_charges.amount * COUNT(id) AS total'),
                    'currency',
                    'billings_charges.step_size',
                    DB::raw('COUNT(id) AS quantity'),
                    'range_start.step_start_amount',
                    'range_end.step_end_amount',
                    DB::raw('0 AS balance'), // Only needed for the group by clause
                    'store_id',
                    'is_billed',
                ])
                ->where('store_id', App::context()->store->id)
                ->where('is_billed', false)
                ->joinSub($minSteps, 'range_start', function (JoinClause $join) {
                    $join->on('billings_charges.amount', '=', 'range_start.amount');
                    $join->on('billings_charges.step_size', '=', 'range_start.step_size');
                })
                ->joinSub($maxSteps, 'range_end', function (JoinClause $join) {
                    $join->on('billings_charges.amount', '=', 'range_end.amount');
                    $join->on('billings_charges.step_size', '=', 'range_end.step_size');
                })
                ->groupBy('billings_charges.amount', 'billings_charges.step_size', 'currency', 'store_id', 'balance', 'is_billed')
                ->orderBy('range_start.step_start_amount', 'asc')
                ->get();

            /** @psalm-suppress InvalidArgument */
            return collect([
                'summary' => ChargeValue::collection($summary),
                'charges' => ChargeValue::collection(Charge::where('is_billed', false)->get()),
            ]);
        });
    }
}
