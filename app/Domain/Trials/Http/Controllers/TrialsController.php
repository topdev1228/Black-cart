<?php
declare(strict_types=1);

namespace App\Domain\Trials\Http\Controllers;

use App\Domain\Shared\Http\Controllers\Controller;
use App\Domain\Trials\Http\Resources\TrialCollection;
use App\Domain\Trials\Http\Resources\TrialResource;
use App\Domain\Trials\Services\TrialService;
use App\Domain\Trials\Values\Trialable as TrialableValue;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Http\Response;

class TrialsController extends Controller
{
    public function __construct(protected TrialService $trialService)
    {
    }

    /**
     * Display a listing of the resource.
     */
    public function index(): Response|JsonResponse
    {
        return $this->sendResponse(
            new TrialCollection($this->trialService->all())
        );
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(TrialableValue $input): Response|JsonResponse
    {
        return $this->sendResponse(
            new TrialResource($this->trialService->create($input)),
            Response::HTTP_CREATED
        );
    }

    /**
     * Display the specified resource.
     */
    public function show(string $trial): Response|JsonResponse
    {
        return $this->sendResponse(
            new TrialResource($this->trialService->getTrialable($trial)),
        );
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, TrialableValue $trial): Response|JsonResponse
    {
        $trialValue = $this->trialService->update($trial, $request->all());

        return $this->sendResponse(
            new TrialResource($trialValue)
        );
    }
}
