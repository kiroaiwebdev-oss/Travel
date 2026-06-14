<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Services\Wallet\WalletService;
use Illuminate\Http\JsonResponse;

class WalletApiController extends Controller
{
    public function __construct(private readonly WalletService $wallet) {}

    public function show(): JsonResponse
    {
        $wallet = $this->wallet->walletFor(auth('api')->user());

        return response()->json(['data' => $wallet]);
    }

    public function transactions(): JsonResponse
    {
        $tx = auth('api')->user()->walletTransactions()
            ->latest()->paginate(20);

        return response()->json($tx);
    }

    public function cashback(): JsonResponse
    {
        $cashback = auth('api')->user()->cashbacks()
            ->with('provider:id,name,slug,logo_url')
            ->latest()->paginate(20);

        return response()->json($cashback);
    }
}
