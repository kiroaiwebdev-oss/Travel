<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\User;
use Illuminate\Contracts\View\View;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;

class KycController extends Controller
{
    public function index(Request $request): View
    {
        $users = User::query()
            ->where('kyc_status', $request->query('status', 'pending'))
            ->latest('kyc_submitted_at')
            ->paginate(25)
            ->withQueryString();

        return view('admin.kyc.index', [
            'users' => $users,
            'status' => $request->query('status', 'pending'),
        ]);
    }

    public function approve(Request $request, User $user): RedirectResponse
    {
        $user->update([
            'kyc_status' => 'approved',
            'kyc_reviewed_at' => now(),
            'kyc_reviewed_by' => $request->user()->id,
            'kyc_note' => null,
        ]);

        return back()->with('status', "{$user->name}'s KYC approved.");
    }

    public function reject(Request $request, User $user): RedirectResponse
    {
        $data = $request->validate(['note' => ['required', 'string', 'max:255']]);
        $user->update([
            'kyc_status' => 'rejected',
            'kyc_reviewed_at' => now(),
            'kyc_reviewed_by' => $request->user()->id,
            'kyc_note' => $data['note'],
        ]);

        return back()->with('status', "{$user->name}'s KYC rejected.");
    }
}
