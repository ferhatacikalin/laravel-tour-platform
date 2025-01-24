<?php

namespace Database\Seeders;

use App\Models\User;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;

class UserSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // Create admin user
        User::create([
            'name' => 'Admin User',
            'email' => 'admin@example.com',
            'password' => Hash::make('password'),
            'role' => 'admin'
        ]);

        // Create test tour operator
        User::create([
            'name' => 'Tour Operator',
            'email' => 'operator@example.com',
            'password' => Hash::make('password'),
            'role' => 'tour_operator'
        ]);

        // Create additional tour operators
        User::factory(3)
            ->state(['role' => 'tour_operator'])
            ->create();
    }
}
