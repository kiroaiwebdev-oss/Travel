<?php

namespace App\Http\Controllers\User;

use App\Http\Controllers\Controller;
use Illuminate\Contracts\View\View;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;

class KycController extends Controller
{
    public function show(Request $request): View
    {
        return view('dashboard.kyc', ['user' => $request->user()]);
    }

    public function submit(Request $request): RedirectResponse
    {
        $user = $request->user();

        if ($user->kyc_status === 'approved') {
            return back()->with('status', 'Your KYC is already approved.');
        }

        $data = $request->validate([
            'kyc_full_name' => ['required', 'string', 'max:120'],
            'kyc_pan' => ['required', 'string', 'regex:/^[A-Z]{5}[0-9]{4}[A-Z]$/'],
            'kyc_payout_method' => ['required', 'in:upi,bank,paypal'],
            'payout' => ['required', 'array'],
        ], [
            'kyc_pan.regex' => 'Enter a valid PAN (e.g. ABCDE1234F).',
        ]);

        $user->update([
            'kyc_full_name' => $data['kyc_full_name'],
            'kyc_pan' => strtoupper($data['kyc_pan']),
            'kyc_payout_method' => $data['kyc_payout_method'],
            'kyc_payout_details' => $data['payout'],
            'kyc_status' => 'pending',
            'kyc_submitted_at' => now(),
            'kyc_note' => null,
        ]);

        return back()->with('status', 'KYC submitted — we will review it shortly.');
    }
}
