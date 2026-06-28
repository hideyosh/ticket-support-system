<?php

namespace Database\Seeders;

use App\Models\User;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;

class UserSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        User::insert([
            ['name' => 'Admin', 'email' => 'admin@admin.com', 'password' => Hash::make('password'), 'created_at' => now(), 'updated_at' => now(), 'role_id' => 4],
            ['name' => 'Supervisor', 'email' => 'supervisor@admin.com', 'password' => Hash::make('password'), 'created_at' => now(), 'updated_at' => now(), 'role_id' => 3],
            ['name' => 'Agent', 'email' => 'agent@admin.com', 'password' => Hash::make('password'), 'created_at' => now(), 'updated_at' => now(), 'role_id' => 2],
            ['name' => 'Customer', 'email' => 'customer@demo.com', 'password' => Hash::make('password'), 'created_at' => now(), 'updated_at' => now(), 'role_id' => 1]
        ]);
    }
}
