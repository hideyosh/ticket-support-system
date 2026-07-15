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
            ['category_name' => 'Software', 'created_at' => now()],
            ['category_name' => 'Hardware', 'created_at' => now()],
            ['category_name' => 'Network', 'created_at' => now()],
            ['category_name' => 'Database', 'created_at' => now()],
            ['category_name' => 'Security', 'created_at' => now()],
        ]);
    }
}
