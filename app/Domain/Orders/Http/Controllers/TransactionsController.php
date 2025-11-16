<?php
declare(strict_types=1);

namespace App\Domain\Orders\Http\Controllers;

use App\Domain\Orders\Enums\TransactionKind;
use App\Domain\Orders\Http\Resources\TransactionsResource;
use App\Domain\Orders\Services\TransactionService;
use App\Domain\Shared\Http\Controllers\Controller;
use Carbon\CarbonImmutable;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Http\Response;
use Symfony\Component\HttpFoundation\StreamedResponse;

class TransactionsController extends Controller
{
    public function __construct(protected TransactionService $transactionService)
    {
    }

    public function get(Request $request): Response|JsonResponse|StreamedResponse
    {
        $startDatetime = CarbonImmutable::now()->firstOfMonth()->startOfDay();
        if ($request->query('start', null) !== null) {
            $startDatetime = CarbonImmutable::parse($request->query('start'));
        }
        $endDatetime = CarbonImmutable::now();
        if ($request->query('end', null) !== null) {
            $endDatetime = CarbonImmutable::parse($request->query('end'));
        }

        $transactionResource = new TransactionsResource(
            $this->transactionService->getTransactionsProcessedAtDatetimeRangeAndKinds(
                $startDatetime,
                $endDatetime,
                [TransactionKind::SALE, TransactionKind::CAPTURE, TransactionKind::REFUND]
            )
        );

        if ($request->query('export', false)) {
            $filename = 'transactions.csv';

            $headers = [
                'Content-Type' => 'text/csv',
                'Content-Disposition' => "attachment; filename=\"$filename\"",
                'Pragma' => 'no-cache',
                'Cache-Control' => 'must-revalidate, post-check=0, pre-check=0',
                'Expires' => '0',
            ];

            return response()->stream(function () use ($request, $transactionResource) {
                $handle = fopen('php://output', 'w');

                // Add CSV headers
                fputcsv($handle, [
                    'transaction_date',
                    'type',
                    'order_number',
                    'amount',
                ]);

                foreach (($transactionResource->toArray($request))['transactions'] as $transaction) {
                    // Write data to a CSV file.
                    fputcsv($handle, [
                        $transaction['date'],
                        $transaction['type'],
                        $transaction['order_number'],
                        $transaction['amount'],
                    ]);
                }

                // Close CSV file handle
                fclose($handle);
            }, 200, $headers);
        }

        return $this->sendResponse(
            $transactionResource
        );
    }
}
