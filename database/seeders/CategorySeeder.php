<?php

namespace Database\Seeders;

use App\Models\Category;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class CategorySeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        Category::insert([
            ['category_name' => 'Technical Support', 'created_at' => now()],
            ['category_name' => 'Account Issue', 'created_at' => now()],
            ['category_name' => 'Billing', 'created_at' => now()],
            ['category_name' => 'Feature Request', 'created_at' => now()],
            ['category_name' => 'Bug Report', 'created_at' => now()],
            ['category_name' => 'Infrastructure', 'created_at' => now()],
        ]);
    }
}
