<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;

class DatabaseSeeder extends Seeder
{
    public function run()
    {
        // Create roles and permissions first
        $this->call(RolePermissionSeeder::class);
        
        // Create admin and sample users
        $this->call(AdminUserSeeder::class);
        
        // Create sample data for development
        if (app()->isLocal()) {
            $this->call(SampleDataSeeder::class);
        }
    }
}
