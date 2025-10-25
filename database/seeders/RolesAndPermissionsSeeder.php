<?php

namespace Database\Seeders;

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

        // ----------------------------------------------------
        // Create Roles
        // ----------------------------------------------------
        $role1 = Role::create(['name' => 'Super-Admin']);
        $role2 = Role::create(['name' => 'Observer']);


        // ----------------------------------------------------
        // Link Permissions to Roles
        // ----------------------------------------------------

        // Super-Admin gets all permissions
        $role1->givePermissionTo(Permission::all());

        // Observer only gets 'see-users'
        $role2->givePermissionTo('see-users');

    }
}

