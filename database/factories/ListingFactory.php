<?php

namespace Database\Factories;

use Illuminate\Database\Eloquent\Factories\Factory;
use App\Models\User;
use App\Models\ListingStatus;
use App\Models\Manufacturer;
use App\Models\Product;
use App\Models\Currency;
use App\Models\Country;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\Listing>
 */
class ListingFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        $useDefaultLocation = $this->faker->boolean(30);

        return [
            'user_id' => User::inRandomOrder()->first()->id,
            'status_id' => ListingStatus::first()->id,
            'manufacturer_id' => Manufacturer::inRandomOrder()->first()->id,
            'title' => Manufacturer::inRandomOrder()->first()->name
                . ' ' . Product::inRandomOrder()->first()->subcategory
                . ' ' . $this->faker->sentence,
            'description' => $this->faker->paragraph,
            'budget_currency_id' => Currency::inRandomOrder()->first()->id,
            'budget' => $this->faker->randomFloat(2, 10, 1000),
            'use_default_location' => $useDefaultLocation,
            'override_address_line1' => $useDefaultLocation ? $this->faker->streetAddress : null,
            'override_address_line2' => $useDefaultLocation ? $this->faker->secondaryAddress : null,
            'override_city' => $useDefaultLocation ? $this->faker->city : null,
            'override_postcode' => $useDefaultLocation ? $this->faker->postcode : null,
            'override_country_id' => $useDefaultLocation ? Country::inRandomOrder()->first()->id : null,
            'expiry_days' => $this->faker->numberBetween(7, 90),
            'published_at' => fake()->optional(0.9)->dateTimeBetween('-1 month', 'now'),
        ];
    }
}
