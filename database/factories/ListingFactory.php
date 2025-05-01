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
        $customer = User::factory()->customer()->verified()->create();
        $useDefaultLocation = $this->faker->boolean(30);

        return [
            'user_id' => $customer->id,
            'status_id' => ListingStatus::first()->id,
            'manufacturer_id' => Manufacturer::inRandomOrder()->first()->id,
            'title' => Product::inRandomOrder()->first()->subcategory
                . ' ' . $this->faker->sentence,
            'description' => $this->faker->paragraph,
            'currency_id' => Currency::inRandomOrder()->first()->id,
            'budget' => $this->faker->randomFloat(2, 10, 1000),
            'use_default_location' => $useDefaultLocation,
            'address_line1' => $useDefaultLocation ? $customer->address_line1 : $this->faker->streetAddress,
            'address_line2' => $useDefaultLocation ? $customer->address_line2 : $this->faker->secondaryAddress,
            'city' => $useDefaultLocation ? $customer->city : $this->faker->city,
            'postcode' => $useDefaultLocation ? $customer->postcode : $this->faker->postcode,
            'country_id' => $useDefaultLocation ? $customer->country_id : Country::inRandomOrder()->first()->id,
            'phone' => $useDefaultLocation ? $customer->phone : $this->faker->phoneNumber,
            'expiry_days' => $this->faker->randomElement([7, 14, 30, 60, 90]),
            'published_at' => ($this->faker->optional(0.9)->dateTimeBetween('-1 month', 'now') ?? now())->format('Y-m-d H:i:s'),
        ];
    }
}
