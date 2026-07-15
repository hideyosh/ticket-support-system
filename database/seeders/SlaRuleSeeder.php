<?php

namespace Database\Seeders;

use App\Models\Priority;
use App\Models\SlaRule;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class SlaRuleSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $priorities = Priority::select('id', 'priority_name')->get();
        foreach ($priorities as $priority) {
            SlaRule::insert([
                'priority_id' => $priority->id,
                'response_time' => match($priority->priority_name) {
                    'Low' => 24,
                    'Medium' => 8,
                    'High' => 4,
                    'Critical' => 1,
                },
                'resolution_time' => match($priority->priority_name) {
                    'Low' => 120,
                    'Medium' => 72,
                    'High' => 24,
                    'Critical' => 8,
                },
                'created_at' => now(),
            ]);
        }
    }
}
