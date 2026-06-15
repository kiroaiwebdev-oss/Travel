<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\User;
use Illuminate\Contracts\View\View;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;

/**
 * Admin composes and broadcasts notifications to users. Stored as in-app
 * notifications (visible immediately in each user's notification list) and, when
 * VAPID web-push is configured, also pushed to the browser.
 */
class NotificationController extends Controller
{
    public function index(Request $request): View
    {
        return view('admin.notifications.index', [
            'recent' => DB::table('notifications')
                ->where('type', 'admin.broadcast')
                ->orderByDesc('created_at')
                ->limit(20)->get(),
            'userCount' => User::where('status', 'active')->count(),
            'webpushReady' => ! empty(config('services.webpush.public_key')),
        ]);
    }

    public function send(Request $request): RedirectResponse
    {
        $data = $request->validate([
            'title' => ['required', 'string', 'max:120'],
            'body' => ['required', 'string', 'max:500'],
            'category' => ['nullable', 'in:general,cashback,promo,system'],
            'audience' => ['required', 'in:all,kyc_approved,with_balance'],
            'url' => ['nullable', 'string', 'max:255'],
        ]);

        $payload = json_encode([
            'title' => $data['title'],
            'message' => $data['body'],
            'url' => $data['url'] ?? '/dashboard/notifications',
        ]);

        $now = now();
        $count = 0;

        $this->audienceQuery($data['audience'])
            ->select('users.id')
            ->chunkById(500, function ($users) use (&$count, $payload, $data, $now) {
                $rows = [];
                foreach ($users as $u) {
                    $rows[] = [
                        'id' => (string) Str::uuid(),
                        'type' => 'admin.broadcast',
                        'notifiable_type' => User::class,
                        'notifiable_id' => $u->id,
                        'data' => $payload,
                        'category' => $data['category'] ?? 'general',
                        'read_at' => null,
                        'created_at' => $now,
                        'updated_at' => $now,
                    ];
                }
                if ($rows) {
                    DB::table('notifications')->insert($rows);
                    $count += count($rows);
                }
            });

        return back()->with('status', "Notification sent to {$count} user(s).");
    }

    private function audienceQuery(string $audience)
    {
        $q = User::query()->where('status', 'active');

        return match ($audience) {
            'kyc_approved' => $q->where('kyc_status', 'approved'),
            'with_balance' => $q->whereHas('wallet', fn ($w) => $w->where('balance', '>', 0)),
            default => $q,
        };
    }
}
