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
            ['label_name' => 'Bug', 'created_at' => now(), 'updated_at' => now()],
            ['label_name' => 'Feature Request', 'created_at' => now(), 'updated_at' => now()],
            ['label_name' => 'Improvement', 'created_at' => now(), 'updated_at' => now()],
            ['label_name' => 'Urgent', 'created_at' => now(), 'updated_at' => now()],
            ['label_name' => 'Question', 'created_at' => now(), 'updated_at' => now()],
            ['label_name' => 'Billing', 'created_at' => now(), 'updated_at' => now()],
            ['label_name' => 'Technical Support', 'created_at' => now(), 'updated_at' => now()],
        ]);
    }
}
