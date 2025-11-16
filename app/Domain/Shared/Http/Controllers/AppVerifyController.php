<?php
declare(strict_types=1);

namespace App\Domain\Shared\Http\Controllers;

use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class AppVerifyController extends Controller
{
    public function secrets(): JsonResponse
    {
        return response()->json(['SECRET_VERIFICATION' => (bool) env('SECRET_VERIFICATION', false)]);
    }

    public function headers(Request $request): JsonResponse
    {
        return response()->json($request->headers->all());
    }
}
