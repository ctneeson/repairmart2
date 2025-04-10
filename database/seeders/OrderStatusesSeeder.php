<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use App\Models\OrderStatus;

class OrderStatusesSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        OrderStatus::factory()
            ->sequence(
                ['name' => 'Created'],
                ['name' => 'Dispatched to Specialist'],
                ['name' => 'Specialist Assessing'],
                ['name' => 'Quote Adjustment Requested'],
                ['name' => 'Quote Adjustment Approved'],
                ['name' => 'Quote Adjustment Rejected'],
                ['name' => 'Specialist Repairing'],
                ['name' => 'Dispatched to Customer'],
                ['name' => 'Closed-Repaired'],
                ['name' => 'Closed-Cancelled'],
            )
            ->count(10)
            ->create();
    }
}
