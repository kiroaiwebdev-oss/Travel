<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Cashback;
use App\Models\User;
use App\Models\Withdrawal;
use Symfony\Component\HttpFoundation\StreamedResponse;

/**
 * CSV report exports for finance/ops reconciliation.
 */
class ReportController extends Controller
{
    public function users(): StreamedResponse
    {
        return $this->stream('users.csv', ['ID', 'Name', 'Email', 'Status', 'KYC', 'Wallet', 'Joined'], function ($out) {
            User::with('wallet')->chunk(500, function ($rows) use ($out) {
                foreach ($rows as $u) {
                    fputcsv($out, [$u->id, $u->name, $u->email, $u->status, $u->kyc_status, $u->wallet?->balance ?? 0, $u->created_at]);
                }
            });
        });
    }

    public function cashbacks(): StreamedResponse
    {
        return $this->stream('cashbacks.csv', ['ID', 'User', 'Provider', 'Category', 'Amount', 'Status', 'Created'], function ($out) {
            Cashback::with('user:id,email', 'provider:id,name')->chunk(500, function ($rows) use ($out) {
                foreach ($rows as $c) {
                    fputcsv($out, [$c->id, $c->user?->email, $c->provider?->name, $c->category, $c->amount, $c->status, $c->created_at]);
                }
            });
        });
    }

    public function withdrawals(): StreamedResponse
    {
        return $this->stream('withdrawals.csv', ['ID', 'User', 'Amount', 'Method', 'Gateway', 'Status', 'Ref', 'Created'], function ($out) {
            Withdrawal::with('user:id,email')->chunk(500, function ($rows) use ($out) {
                foreach ($rows as $w) {
                    fputcsv($out, [$w->id, $w->user?->email, $w->amount, $w->method, $w->gateway, $w->status, $w->gateway_payout_id ?? $w->reference, $w->created_at]);
                }
            });
        });
    }

    private function stream(string $filename, array $header, callable $writer): StreamedResponse
    {
        return response()->streamDownload(function () use ($header, $writer) {
            $out = fopen('php://output', 'w');
            fputcsv($out, $header);
            $writer($out);
            fclose($out);
        }, $filename, ['Content-Type' => 'text/csv']);
    }
}
