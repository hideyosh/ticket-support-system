<?php

namespace Database\Seeders;

use App\Models\Priority;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class PrioritySeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        Priority::insert([
            ['priority_name' => 'Low', 'created_at' => now()],
            ['priority_name' => 'Medium', 'created_at' => now()],
            ['priority_name' => 'High', 'created_at' => now()],
            ['priority_name' => 'Critical', 'created_at' => now()],
        ]);
    }
}
