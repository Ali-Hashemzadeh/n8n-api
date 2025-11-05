<?php

namespace Database\Seeders;

use App\Models\User;
use Illuminate\Database\Seeder;
use Spatie\Permission\Models\Role;
use Spatie\Permission\Models\Permission;

class RolesAndPermissionsSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // Reset cached roles and permissions
        app()[\Spatie\Permission\PermissionRegistrar::class]->forgetCachedPermissions();

        // ----------------------------------------------------
        // Create Permissions
        // ----------------------------------------------------
        Permission::create(['name' => 'manage-roles']);
        Permission::create(['name' => 'manage-perms']);
        Permission::create(['name' => 'manage-users']);
        Permission::create(['name' => 'see-users']);
        Permission::create(['name' => 'manage-service-types']); // From previous step
        Permission::create(['name' => 'manage-companies']); // From previous step
        Permission::create(['name' => 'see-call-reports']); // <-- ADD THIS

        // ----------------------------------------------------
        // Create Roles
        // ----------------------------------------------------
        $role1 = Role::create(['name' => 'Super-Admin']);
        $role2 = Role::create(['name' => 'Admin']);
        $role3 = Role::create(['name' => 'Observer']);


        // ----------------------------------------------------
        // Link Permissions to Roles
        // ----------------------------------------------------

        // Super-Admin gets all permissions
        $role1->givePermissionTo(Permission::all());

        // Admin
        $role2->givePermissionTo('see-users');
        $role2->givePermissionTo('manage-service-types');
        $role2->givePermissionTo('see-call-reports'); // <-- ADD THIS

        // Observer only gets 'see-users'
        $role3->givePermissionTo('see-users');

        $user = User::where('email', 'ali.melmedas1383@gmail.com')->first();
        if ($user) {
            $user->assignRole('Super-Admin');
        }

    }
}
