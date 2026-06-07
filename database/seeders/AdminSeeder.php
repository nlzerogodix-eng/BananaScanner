<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\User;
use Illuminate\Support\Facades\Hash;

class AdminSeeder extends Seeder
{
    public function run(): void
    {
        // Check if admin already exists
        $existingAdmin = User::where('email', 'admin@bananascan.com')->first();
        
        if (!$existingAdmin) {
            User::create([
                'name' => 'Administrator',
                'email' => 'admin@bananascan.com',
                'password' => Hash::make('password123'), // Default password
                'is_admin' => true,
                'phone' => '+63 926 508 6466',
                'location' => 'Philippines',
                'bio' => 'System Administrator - Banana Scan',
            ]);
            
            $this->command->info('Admin user created successfully!');
            $this->command->info('Email: admin@bananascan.com');
            $this->command->info('Password: password123');
            $this->command->info('IMPORTANT: Please change the password after first login!');
        } else {
            $this->command->info('Admin user already exists.');
        }
    }
}