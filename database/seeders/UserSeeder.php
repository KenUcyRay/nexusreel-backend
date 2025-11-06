<?php

namespace Database\Seeders;

use App\Models\User;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;

class UserSeeder extends Seeder
{
    public function run(): void
    {
        User::create([
            'name' => 'Admin User',
            'email' => 'admin@cinema.com',
            'password' => Hash::make('password'),
            'phone' => '081234567890',
            'role' => 'admin',
        ]);

        User::create([
            'name' => 'Owner User',
            'email' => 'owner@cinema.com',
            'password' => Hash::make('password'),
            'phone' => '081234567891',
            'role' => 'owner',
        ]);

        User::create([
            'name' => 'Kasir User',
            'email' => 'kasir@cinema.com',
            'password' => Hash::make('password'),
            'phone' => '081234567892',
            'role' => 'kasir',
        ]);

        User::create([
            'name' => 'Regular User',
            'email' => 'user@cinema.com',
            'password' => Hash::make('password'),
            'phone' => '081234567893',
            'role' => 'user',
        ]);
    }
}