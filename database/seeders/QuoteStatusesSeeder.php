<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use App\Models\QuoteStatus;

class QuoteStatusesSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        QuoteStatus::factory()
            ->sequence(
                ['name' => 'Open'],
                ['name' => 'Closed-Rejected'],
                ['name' => 'Closed-Retracted'],
                ['name' => 'Closed-Order Created'],
            )
            ->count(4)
            ->create();
    }
}
