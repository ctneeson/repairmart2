<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use App\Models\DeliveryMethod;

class DeliveryMethodsSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        DeliveryMethod::factory()
            ->sequence(
                ['name' => 'Pick-up/Drop-off at Customer'],
                ['name' => 'Drop-off/Pick-up at Repair Specialist'],
                ['name' => 'Postage (tracked)'],
                ['name' => 'Postage (untracked)'],
            )
            ->count(4)
            ->create();
    }
}
