<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\User;
use Illuminate\Support\Facades\Hash;

class AdminUserSeeder extends Seeder
{
    public function run(): void
    {
        User::create([
            'name' => 'Admin User',
            'email' => 'admin@bananascan.com',
            'password' => Hash::make('admin123'),
            'is_admin' => true,
            'phone' => '+63 926 508 6466',
            'location' => 'Philippines',
            'bio' => 'System Administrator',
        ]);
        
        $this->command->info('Admin user created!');
        $this->command->info('Email: admin@bananascan.com');
        $this->command->info('Password: admin123');
    }
}