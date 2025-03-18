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
            'title' => Product::inRandomOrder()->first()->subcategory
                . ' ' . $this->faker->sentence,
            'description' => $this->faker->paragraph,
            'budget_currency_id' => Currency::inRandomOrder()->first()->id,
            'budget' => $this->faker->randomFloat(2, 10, 1000),
            'use_default_location' => $useDefaultLocation,
            'override_address_line1' => $useDefaultLocation ? null : $this->faker->streetAddress,
            'override_address_line2' => $useDefaultLocation ? null : $this->faker->secondaryAddress,
            'override_city' => $useDefaultLocation ? null : $this->faker->city,
            'override_postcode' => $useDefaultLocation ? null : $this->faker->postcode,
            'override_country_id' => $useDefaultLocation ? null : Country::inRandomOrder()->first()->id,
            'expiry_days' => $this->faker->numberBetween(7, 90),
            'published_at' => ($this->faker->optional(0.9)->dateTimeBetween('-1 month', 'now') ?? now())->format('Y-m-d H:i:s'),
        ];
    }
}
