<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Services\Wallet\WalletService;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class WalletApiController extends Controller
{
    public function __construct(private readonly WalletService $wallet) {}

    public function show(Request $request): JsonResponse
    {
        return response()->json(['data' => $this->wallet->walletFor($request->user())]);
    }

    public function transactions(Request $request): JsonResponse
    {
        return response()->json(
            $request->user()->walletTransactions()->latest()->paginate(20)
        );
    }

    public function cashback(Request $request): JsonResponse
    {
        return response()->json(
            $request->user()->cashbacks()
                ->with('provider:id,name,slug,logo_url')
                ->latest()->paginate(20)
        );
    }
}
