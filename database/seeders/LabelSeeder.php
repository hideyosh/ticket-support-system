<?php

namespace Database\Seeders;

use App\Models\Label;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class LabelSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        Label::insert([
            ['label_name' => 'Urgent', 'created_at' => now(), 'updated_at' => now()],
            ['label_name' => 'Backend', 'created_at' => now(), 'updated_at' => now()],
            ['label_name' => 'Frontend', 'created_at' => now(), 'updated_at' => now()],
            ['label_name' => 'Database', 'created_at' => now(), 'updated_at' => now()],
            ['label_name' => 'Security', 'created_at' => now(), 'updated_at' => now()],
            ['label_name' => 'Needs Follow Up', 'created_at' => now(), 'updated_at' => now()],
            ['label_name' => 'Customer Waiting', 'created_at' => now(), 'updated_at' => now()],
        ]);
    }
}
