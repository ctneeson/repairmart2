<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use App\Models\FeedbackType;

class FeedbackTypesSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        FeedbackType::factory()
            ->sequence(
                ['name' => 'Positive'],
                ['name' => 'Neutral'],
                ['name' => 'Negative'],
            )
            ->count(3)
            ->create();
    }
}
