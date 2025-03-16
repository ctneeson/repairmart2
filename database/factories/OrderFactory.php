<?php

namespace Database\Factories;

use Illuminate\Database\Eloquent\Factories\Factory;
use App\Models\Listing;
use App\Models\Quote;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\Order>
 */
class OrderFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        $overrideQuote = $this->faker->boolean(20);

        $listing = Listing::inRandomOrder()->first();
        $listingId = $listing->id;
        $quote = Quote::where('listing_id', $listingId)->inRandomOrder()->first();
        $quoteId = $quote ? $quote->id : null;

        return [
            'listing_id' => $listingId,
            'quote_id' => $quoteId,
            'status_id' => 1, // Default status is pending
            'override_quote' => $overrideQuote,
            'override_amount' => $overrideQuote ? $this->faker->randomFloat(2, 10, 1000) : null,
        ];
    }
}
