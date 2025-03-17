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
                ['name' => 'Drop-off at Customer address'],
                ['name' => 'Pick-up at Business address'],
                ['name' => 'Postage (tracked)'],
                ['name' => 'Postage (untracked)'],
            )
            ->count(4)
            ->create();
    }
}
