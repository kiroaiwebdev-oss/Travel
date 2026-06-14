<?php

namespace Database\Seeders;

use App\Models\Permission;
use App\Models\Role;
use Illuminate\Database\Seeder;

class RolePermissionSeeder extends Seeder
{
    public function run(): void
    {
        $permissions = [
            'dashboard' => ['admin.access' => 'Access admin panel'],
            'users' => ['users.view' => 'View users', 'users.manage' => 'Manage users'],
            'providers' => ['providers.view' => 'View providers', 'providers.manage' => 'Manage providers & API keys'],
            'wallet' => ['wallets.view' => 'View wallets', 'withdrawals.approve' => 'Approve withdrawals'],
            'cashback' => ['cashback.view' => 'View cashback', 'cashback.manage' => 'Manage cashback rules'],
            'cms' => ['cms.manage' => 'Manage CMS content'],
            'support' => ['support.handle' => 'Handle support tickets'],
            'settings' => ['settings.manage' => 'Manage settings'],
            'analytics' => ['analytics.view' => 'View analytics'],
            'audit' => ['audit.view' => 'View audit logs'],
        ];

        $created = [];
        foreach ($permissions as $group => $perms) {
            foreach ($perms as $name => $label) {
                $created[$name] = Permission::updateOrCreate(
                    ['name' => $name],
                    ['group' => $group, 'label' => $label]
                );
            }
        }

        $roles = [
            'admin' => ['label' => 'Administrator', 'desc' => 'Full platform control', 'perms' => array_keys($created)],
            'manager' => ['label' => 'Manager', 'desc' => 'Operations management', 'perms' => [
                'admin.access', 'users.view', 'providers.view', 'providers.manage',
                'wallets.view', 'withdrawals.approve', 'cashback.view', 'cashback.manage',
                'analytics.view',
            ]],
            'support' => ['label' => 'Support Agent', 'desc' => 'Customer support', 'perms' => [
                'admin.access', 'users.view', 'support.handle', 'wallets.view',
            ]],
            'user' => ['label' => 'User', 'desc' => 'Standard member', 'perms' => []],
        ];

        foreach ($roles as $name => $data) {
            $role = Role::updateOrCreate(
                ['name' => $name],
                ['label' => $data['label'], 'description' => $data['desc']]
            );
            $ids = collect($data['perms'])->map(fn ($p) => $created[$p]->id)->all();
            $role->permissions()->sync($ids);
        }
    }
}
