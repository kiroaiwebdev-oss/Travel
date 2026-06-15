<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\AuditLog;
use Illuminate\Contracts\View\View;
use Illuminate\Http\Request;

class AuditLogController extends Controller
{
    public function index(Request $request): View
    {
        $logs = AuditLog::query()
            ->with('user:id,name,email')
            ->when($request->query('action'), fn ($q, $a) => $q->where('action', 'like', "%{$a}%"))
            ->latest('created_at')
            ->paginate(40)
            ->withQueryString();

        return view('admin.audit.index', ['logs' => $logs, 'q' => $request->query('action')]);
    }
}
