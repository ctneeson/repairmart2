<?php

namespace Database\Factories;

use App\Models\FeedbackType;
use Illuminate\Database\Eloquent\Factories\Factory;
use App\Models\Listing;
use App\Models\Quote;
use App\Models\User;

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

        $customer = User::whereHas('roles', function ($query) {
            $query->where('name', 'customer');
        })
            ->where('is_verified', true)
            ->inRandomOrder()
            ->first();
        $specialist = User::whereHas('roles', function ($query) {
            $query->where('name', 'specialist');
        })
            ->where('is_verified', true)
            ->inRandomOrder()
            ->first();
        $listing = Listing::inRandomOrder()->first();
        $quote = Quote::factory()->create(['listing_id' => $listing->id, 'user_id' => $specialist->id]);

        return [
            'quote_id' => $quote->id,
            'customer_id' => $customer->id,
            'specialist_id' => $specialist->id,
            'status_id' => 1, // Default status is pending
            'override_quote' => $overrideQuote,
            'amount' => $overrideQuote ? $this->faker->randomFloat(2, 10, 1000) : null,
            'customer_feedback_id' => FeedbackType::inRandomOrder()->first()->id,
            'customer_feedback' => $this->faker->text(200),
            'specialist_feedback_id' => FeedbackType::inRandomOrder()->first()->id,
            'specialist_feedback' => $this->faker->text(200),
        ];
    }
}
