<?php

namespace Database\Seeders;

use App\Models\User;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;

class AdminUserSeeder extends Seeder
{
    public function run()
    {
        // Create admin user
        $admin = User::firstOrCreate(
            ['email' => 'admin@appiahkubi.edu.gh'],
            [
                'name' => 'System Administrator',
                'phone' => '0234567890',
                'graduation_year' => '2000',
                'house' => 'Excellence',
                'current_profession' => 'System Administrator',
                'current_company' => 'Appiah Kubi JHS',
                'current_city' => 'Accra',
                'current_country' => 'Ghana',
                'password' => Hash::make('admin123'),
                'email_verified_at' => now(),
                'is_verified' => true,
            ]
        );
        $admin->assignRole('admin');

        // Create sample alumni users
        $alumniUsers = [
            [
                'name' => 'Dr. Kwame Mensah',
                'email' => 'kwame.mensah@example.com',
                'graduation_year' => '1995',
                'house' => 'Perseverance',
                'current_profession' => 'Medical Doctor',
                'current_company' => 'Korle-Bu Teaching Hospital',
                'current_city' => 'Accra',
            ],
            [
                'name' => 'Prof. Ama Serwaa',
                'email' => 'ama.serwaa@example.com',
                'graduation_year' => '1998',
                'house' => 'Integrity',
                'current_profession' => 'University Professor',
                'current_company' => 'University of Ghana',
                'current_city' => 'Legon',
            ],
            [
                'name' => 'Mr. Kofi Asare',
                'email' => 'kofi.asare@example.com',
                'graduation_year' => '2005',
                'house' => 'Unity',
                'current_profession' => 'Software Engineer',
                'current_company' => 'Tech Solutions Ltd',
                'current_city' => 'Kumasi',
            ],
        ];

        foreach ($alumniUsers as $userData) {
            $user = User::firstOrCreate(
                ['email' => $userData['email']],
                [
                    ...$userData,
                    'phone' => '0234567' . rand(100, 999),
                    'current_country' => 'Ghana',
                    'password' => Hash::make('password123'),
                    'email_verified_at' => now(),
                    'is_verified' => true,
                ]
            );
            $user->assignRole('alumni');
        }

        $this->command->info('Default users created successfully!');
        $this->command->info('Admin: admin@appiahkubi.edu.gh / admin123');
        $this->command->info('Sample alumni users created with password: password123');
    }
}
