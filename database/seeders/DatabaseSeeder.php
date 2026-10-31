<?php

namespace Database\Seeders;

use App\Models\Customer;
use App\Models\User;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class DatabaseSeeder extends Seeder
{
    use WithoutModelEvents;

    /**
     * Seed the application's database.
     */
    public function run(): void
    {
        $customers = [
            ['full_name' => 'Maria Santos', 'email' => 'maria@example.com', 'phone' => '0917 123 4567'],
            ['full_name' => 'Juan Dela Cruz', 'email' => 'juan@example.com', 'phone' => '0918 234 5678'],
            ['full_name' => 'Ana Reyes', 'email' => 'ana@example.com', 'phone' => '0919 345 6789'],
            ['full_name' => 'Paolo Garcia', 'email' => 'paolo@example.com', 'phone' => '0920 456 7890'],
            ['full_name' => 'Lea Mendoza', 'email' => 'lea@example.com', 'phone' => '0921 567 8901'],
        ];

        foreach ($customers as $customer) {
            Customer::firstOrCreate(['email' => $customer['email']], $customer);
        }

        $users = [
            ['username' => 'sean', 'full_name' => 'Sean Admin', 'role' => 'Admin'],
            ['username' => 'carla', 'full_name' => 'Carla Ramos', 'role' => 'Manager'],
            ['username' => 'miguel', 'full_name' => 'Miguel Tan', 'role' => 'Cashier'],
            ['username' => 'isa', 'full_name' => 'Isa Cruz', 'role' => 'Cashier'],
            ['username' => 'rico', 'full_name' => 'Rico Lim', 'role' => 'Manager'],
        ];

        foreach ($users as $user) {
            User::firstOrCreate(['username' => $user['username']], $user);
        }
    }
}
