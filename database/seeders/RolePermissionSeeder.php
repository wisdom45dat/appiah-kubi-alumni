<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Spatie\Permission\Models\Role;
use Spatie\Permission\Models\Permission;

class RolePermissionSeeder extends Seeder
{
    public function run()
    {
        // Reset cached roles and permissions
        app()[\Spatie\Permission\PermissionRegistrar::class]->forgetCachedPermissions();

        // Create permissions
        $permissions = [
            // User permissions
            'view alumni directory',
            'edit own profile',
            'view all profiles',
            
            // Gallery permissions
            'create albums',
            'upload media',
            'view all albums',
            'comment on media',
            'like media',
            
            // Event permissions
            'create events',
            'register for events',
            'view all events',
            'manage events',
            
            // Donation permissions
            'make donations',
            'view campaigns',
            'create campaigns',
            
            // Jobs permissions
            'post jobs',
            'view jobs',
            'apply for jobs',
            
            // Forum permissions
            'create topics',
            'reply to topics',
            'view forum',
            
            // News permissions
            'create news',
            'publish news',
            'view news',
            
            // Admin permissions
            'access admin',
            'manage users',
            'manage content',
            'view reports',
            'manage settings',
        ];

        foreach ($permissions as $permission) {
            Permission::firstOrCreate(['name' => $permission, 'guard_name' => 'web']);
        }

        // Create roles and assign permissions
        $adminRole = Role::firstOrCreate(['name' => 'admin', 'guard_name' => 'web']);
        $adminRole->syncPermissions($permissions);

        $alumniRole = Role::firstOrCreate(['name' => 'alumni', 'guard_name' => 'web']);
        $alumniRole->syncPermissions([
            'view alumni directory',
            'edit own profile',
            'view all profiles',
            'create albums',
            'upload media',
            'view all albums',
            'comment on media',
            'like media',
            'create events',
            'register for events',
            'view all events',
            'make donations',
            'view campaigns',
            'post jobs',
            'view jobs',
            'apply for jobs',
            'create topics',
            'reply to topics',
            'view forum',
            'create news',
            'view news',
        ]);

        $memberRole = Role::firstOrCreate(['name' => 'member', 'guard_name' => 'web']);
        $memberRole->syncPermissions([
            'view alumni directory',
            'edit own profile',
            'view all albums',
            'comment on media',
            'like media',
            'register for events',
            'view all events',
            'make donations',
            'view campaigns',
            'view jobs',
            'apply for jobs',
            'create topics',
            'reply to topics',
            'view forum',
            'view news',
        ]);

        $this->command->info('Roles and permissions seeded successfully!');
    }
}
