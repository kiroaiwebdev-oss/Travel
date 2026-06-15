<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Role;
use App\Models\User;
use Illuminate\Contracts\View\View;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Validation\Rules\Password;

/**
 * Staff & role management. Lets an admin create staff accounts and assign roles
 * (admin / manager / support). Drives who can access which admin areas (RBAC).
 */
class StaffController extends Controller
{
    public function index(): View
    {
        $staff = User::whereHas('roles', fn ($q) => $q->whereIn('name', ['admin', 'manager', 'support']))
            ->with('roles')->latest()->paginate(25);

        return view('admin.staff.index', [
            'staff' => $staff,
            'roles' => Role::orderBy('name')->get(),
        ]);
    }

    public function store(Request $request): RedirectResponse
    {
        $data = $request->validate([
            'name' => ['required', 'string', 'max:120'],
            'email' => ['required', 'email', 'unique:users,email'],
            'password' => ['required', Password::min(8)->letters()->numbers()],
            'roles' => ['required', 'array', 'min:1'],
            'roles.*' => ['exists:roles,id'],
        ]);

        $user = User::create([
            'name' => $data['name'],
            'email' => $data['email'],
            'password' => $data['password'], // hashed by cast
            'email_verified_at' => now(),
            'status' => 'active',
        ]);
        $user->roles()->sync($data['roles']);

        return back()->with('status', "Staff account created for {$user->email}.");
    }

    public function updateRoles(Request $request, User $user): RedirectResponse
    {
        $data = $request->validate([
            'roles' => ['array'],
            'roles.*' => ['exists:roles,id'],
        ]);

        // Safety: never let an admin strip the last admin's access (lockout guard).
        $adminRoleId = Role::where('name', 'admin')->value('id');
        $removingAdmin = $user->hasRole('admin') && ! in_array((string) $adminRoleId, $data['roles'] ?? [], true);
        if ($removingAdmin && User::whereHas('roles', fn ($q) => $q->where('name', 'admin'))->count() <= 1) {
            return back()->withErrors(['roles' => 'Cannot remove the last administrator.']);
        }

        $user->roles()->sync($data['roles'] ?? []);

        return back()->with('status', "Roles updated for {$user->name}.");
    }
}
