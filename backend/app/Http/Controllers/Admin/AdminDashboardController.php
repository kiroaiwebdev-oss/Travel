<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Booking;
use App\Models\Cashback;
use App\Models\Provider;
use App\Models\SearchLog;
use App\Models\User;
use App\Models\Withdrawal;
use Illuminate\Contracts\View\View;
use Illuminate\Support\Facades\DB;

class AdminDashboardController extends Controller
{
    public function index(): View
    {
        return view('admin.dashboard', [
            'stats' => [
                'users' => User::count(),
                'providers' => Provider::active()->count(),
                'bookings' => Booking::count(),
                'gmv' => (float) Booking::whereIn('status', ['confirmed', 'completed'])->sum('amount'),
                'commission' => (float) Booking::sum('commission_amount'),
                'cashback_pending' => (float) Cashback::where('status', 'pending')->sum('amount'),
                'cashback_paid' => (float) Cashback::where('status', 'paid')->sum('amount'),
                'withdrawals_pending' => Withdrawal::where('status', 'requested')->count(),
                'searches_today' => SearchLog::whereDate('created_at', today())->count(),
            ],
            'recentBookings' => Booking::with('provider', 'user')->latest()->limit(8)->get(),
            'pendingWithdrawals' => Withdrawal::with('user')->where('status', 'requested')->latest()->limit(8)->get(),
        ]);
    }

    public function analytics(): View
    {
        // Searches per day (last 14 days) + top categories — basis for charts.
        $searchesByDay = SearchLog::query()
            ->where('created_at', '>=', now()->subDays(14))
            ->select(DB::raw('DATE(created_at) as d'), DB::raw('COUNT(*) as c'))
            ->groupBy('d')->orderBy('d')->pluck('c', 'd');

        $topCategories = SearchLog::query()
            ->where('created_at', '>=', now()->subDays(30))
            ->select('category', DB::raw('COUNT(*) as c'))
            ->groupBy('category')->orderByDesc('c')->pluck('c', 'category');

        return view('admin.analytics', [
            'searchesByDay' => $searchesByDay,
            'topCategories' => $topCategories,
            'revenueByProvider' => Booking::query()
                ->select('provider_id', DB::raw('SUM(commission_amount) as commission'))
                ->with('provider:id,name')
                ->groupBy('provider_id')->orderByDesc('commission')->limit(10)->get(),
        ]);
    }
}
