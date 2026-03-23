<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Spatie\Permission\Models\Role;
use Spatie\Permission\Models\Permission;
use App\Models\User;

class RoleSeeder extends Seeder
{
    public function run(): void
    {
        // Reset cached roles and permissions
        app()[\Spatie\Permission\PermissionRegistrar::class]->forgetCachedPermissions();

        // Create Permissions
        Permission::create(['name' => 'manage views']);
        Permission::create(['name' => 'view reports']);
        Permission::create(['name' => 'delete reports']);

        // Create Roles and assign permissions
        $admin = Role::create(['name' => 'Admin']);
        $admin->givePermissionTo(Permission::all());

        $userRole = Role::create(['name' => 'User']);
        $userRole->givePermissionTo(['view reports']);

        // Assign Admin role to existing admin user
        $adminUser = User::where('email', 'admin@example.com')->first();
        if ($adminUser) {
            $adminUser->assignRole($admin);
        }
    }
}
