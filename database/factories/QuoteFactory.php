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
        $listingId = $listing->id;
        $userId = $listing->user_id;
        $currencyId = $listing->currency_id;
        $useDefaultLocation = $this->faker->boolean(20);

        return [
            'user_id' => $userId,
            'listing_id' => $listingId,
            'status_id' => 1,
            'currency_id' => $currencyId,
            'deliverymethod_id' => DeliveryMethod::inRandomOrder()->first()->id,
            'amount' => $this->faker->randomFloat(2, 10, 1000),
            'turnaround' => $this->faker->randomInteger(5, 30),
            'use_default_location' => $useDefaultLocation,
            'override_address_line1' => $useDefaultLocation ? $this->faker->streetAddress : null,
            'override_address_line2' => $useDefaultLocation ? $this->faker->secondaryAddress : null,
            'override_city' => $useDefaultLocation ? $this->faker->city : null,
            'override_postcode' => $useDefaultLocation ? $this->faker->postcode : null,
            'override_country_id' => $useDefaultLocation ? Country::inRandomOrder()->first()->id : null,
        ];
    }
}
