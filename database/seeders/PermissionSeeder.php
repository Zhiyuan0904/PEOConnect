<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Spatie\Permission\Models\Permission;
use Spatie\Permission\Models\Role;

class PermissionSeeder extends Seeder
{
    public function run()
    {
        // Define permissions
        $permissions = [
            'manage users',
            'view reports',
            'edit content',
            'approve requests',
            'access dashboard'
        ];

        // Create permissions
        foreach ($permissions as $permission) {
            Permission::firstOrCreate(['name' => $permission, 'guard_name' => 'web']);
        }

        // Assign permissions to roles
        $admin = Role::where('name', 'admin')->first();
        $admin->givePermissionTo($permissions); // Admin gets all permissions

        $qualityTeam = Role::where('name', 'quality team')->first();
        $qualityTeam->givePermissionTo(['view reports', 'approve requests']);

        $dean = Role::where('name', 'dean')->first();
        $dean->givePermissionTo(['view reports', 'approve requests']);

        $lecturer = Role::where('name', 'lecturer')->first();
        $lecturer->givePermissionTo(['edit content']);

        $alumni = Role::where('name', 'alumni')->first();
        $alumni->givePermissionTo(['access dashboard']);

        $students = Role::where('name', 'students')->first();
        $students->givePermissionTo(['access dashboard']);
    }
}
