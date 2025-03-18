<?php

namespace Database\Factories;

use Illuminate\Database\Eloquent\Factories\Factory;
use App\Models\Listing;
use App\Models\DeliveryMethod;
use App\Models\Country;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\Quote>
 */
class QuoteFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        $listing = Listing::inRandomOrder()->first();
        $listingId = $listing ? $listing->id : null;
        $userId = $listing ? $listing->user_id : null;
        $currencyId = $listing ? $listing->currency_id : null;
        $useDefaultLocation = $this->faker->boolean(20);

        return [
            'user_id' => $userId,
            'listing_id' => $listingId,
            'status_id' => 1, // Ensure status_id is assigned
            'currency_id' => $currencyId,
            'deliverymethod_id' => DeliveryMethod::inRandomOrder()->first()->id,
            'amount' => $this->faker->randomFloat(2, 10, 1000),
            'turnaround' => $this->faker->numberBetween(5, 30),
            'use_default_location' => $useDefaultLocation,
            'override_address_line1' => $useDefaultLocation ? null : $this->faker->streetAddress,
            'override_address_line2' => $useDefaultLocation ? null : $this->faker->secondaryAddress,
            'override_city' => $useDefaultLocation ? null : $this->faker->city,
            'override_postcode' => $useDefaultLocation ? null : $this->faker->postcode,
            'override_country_id' => $useDefaultLocation ? null : Country::inRandomOrder()->first()->id,
        ];
    }
}