<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use App\Models\ListingStatus;

class ListingStatusesSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        ListingStatus::factory()
            ->sequence(
                ['name' => 'Open'],
                ['name' => 'Closed-Expired'],
                ['name' => 'Closed-Retracted'],
                ['name' => 'Closed-Order Created'],
            )
            ->count(4)
            ->create();
    }
}
