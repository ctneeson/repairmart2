<?php

namespace Database\Seeders;

use App\Models\User;
// use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use App\Models\CarType;
use App\Models\FuelType;
use App\Models\State;
use App\Models\City;
use App\Models\Maker;
use App\Models\Car;
use App\Models\CarImage;
use App\Models\Model as CarModel;
use Illuminate\Database\Eloquent\Factories\Sequence;

class DatabaseSeeder extends Seeder
{
    /**
     * Seed the application's database.
     */
    public function run(): void
    {
        // User::factory(10)->create();

        // User::factory()->create([
        //     'name' => 'Test User',
        //     'email' => 'test@example.com',
        // ]);

        // $this->call(UserSeeder::class);

        // >> Create car types with the following data using Factories
        CarType::factory()
            ->sequence(
                ['name' => 'Sedan'],
                ['name' => 'SUV'],
                ['name' => 'Hatchback'],
                ['name' => 'Coupe'],
                ['name' => 'Convertible'],
                ['name' => 'Wagon'],
                ['name' => 'Van'],
                ['name' => 'Truck'],
            )
            ->count(8)
            ->create();

        // >> Create fuel types
        FuelType::factory()
            ->sequence(
                ['name' => 'Petrol'],
                ['name' => 'Electric'],
                ['name' => 'Diesel'],
                ['name' => 'Hybrid'],
                ['name' => 'Hydrogen']
            )
            ->count(5)
            ->create();

        // >> Create States with Cities
        $states = [
            'California' => ['Los Angeles', 'San Francisco', 'San Diego'],
            'Texas' => ['Houston', 'Dallas', 'Austin'],
            'New York' => ['New York City', 'Buffalo', 'Rochester'],
            'Florida' => ['Miami', 'Orlando', 'Tampa'],
            'Illinois' => ['Chicago', 'Springfield', 'Peoria'],
            'Pennsylvania' => ['Philadelphia', 'Pittsburgh', 'Allentown'],
            'Ohio' => ['Columbus', 'Cleveland', 'Cincinnati'],
            'Georgia' => ['Atlanta', 'Savannah', 'Augusta'],
            'North Carolina' => ['Charlotte', 'Raleigh', 'Greensboro'],
        ];

        foreach ($states as $state => $cities) {
            State::factory()
                ->state(['name' => $state])
                ->has(
                    City::factory()
                        ->count(count($cities))
                        ->sequence(...array_map(fn($city) => ['name' => $city], $cities))
                )->create();
        }

        // >> Create makers with corresponding models
        $makers = [
            'Toyota' => ['Camry', 'Corolla', 'RAV4', 'Highlander', 'Sienna'],
            'Honda' => ['Accord', 'Civic', 'CR-V', 'Pilot', 'Odyssey'],
            'Ford' => ['F-150', 'Escape', 'Explorer', 'Mustang', 'Expedition'],
            'Chevrolet' => ['Silverado', 'Equinox', 'Traverse', 'Tahoe', 'Suburban'],
            'Nissan' => ['Altima', 'Sentra', 'Rogue', 'Pathfinder', 'Armada'],
            'Jeep' => ['Wrangler', 'Grand Cherokee', 'Cherokee', 'Compass', 'Renegade'],
            'Hyundai' => ['Elantra', 'Sonata', 'Tucson', 'Santa Fe', 'Palisade'],
        ];

        foreach ($makers as $maker => $models) {
            Maker::factory()
                ->state(['name' => $maker])
                ->has(
                    CarModel::factory()
                        ->count(count($models))
                        ->sequence(...array_map(fn($model) => ['name' => $model], $models))
                )->create();
        }


        // >> Create users, cars with images & features
        // >> Create 3 users first, then 2 more users
        // >> For each user create 50 cars with images & features
        // >> Add these to the favourite cars of the 2 users
        User::factory()
            ->count(3)
            ->create();

        User::factory()
            ->count(2)
            ->has(
                Car::factory()
                    ->count(50)
                    ->has(
                        CarImage::factory()
                            ->count(5)
                            ->sequence(fn(Sequence $sequence) => ['position' => $sequence->index % 5 + 1]),
                        // ->sequence(
                        //     ['position' => 1],
                        //     ['position' => 2],
                        //     ['position' => 3],
                        //     ['position' => 4],
                        //     ['position' => 5]
                        // ),
                        'images'
                    )
                    ->hasFeatures(),
                'favouriteCars'
            )->create();
    }
}
