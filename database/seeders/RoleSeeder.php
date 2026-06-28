<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use App\Models\Role;

class RoleSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        Role::insert([
            ['role_name' => 'customer', 'created_at' => now(), 'updated_at' => now()],
            ['role_name' => 'agent', 'created_at' => now(), 'updated_at' => now()],
            ['role_name' => 'supervisor', 'created_at' => now(), 'updated_at' => now()],
            ['role_name' => 'admin', 'created_at' => now(), 'updated_at' => now()],
        ]);
    }
}
