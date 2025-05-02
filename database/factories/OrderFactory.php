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

        // Get or create necessary models for a default order
        $customer = User::whereHas('roles', function ($query) {
            $query->where('name', 'customer');
        })
            ->where('is_verified', true)
            ->inRandomOrder()
            ->first();

        if (!$customer) {
            $customer = User::factory()->customer()->verified()->create();
        }

        $specialist = User::whereHas('roles', function ($query) {
            $query->where('name', 'specialist');
        })
            ->where('is_verified', true)
            ->inRandomOrder()
            ->first();

        if (!$specialist) {
            $specialist = User::factory()->specialist()->verified()->create();
        }

        $listing = Listing::inRandomOrder()->first();
        if (!$listing) {
            $listing = Listing::factory()->create(['user_id' => $customer->id]);
        }

        $quote = Quote::factory()->create(['listing_id' => $listing->id, 'user_id' => $specialist->id]);

        // Get feedback types
        $feedbackType1 = FeedbackType::inRandomOrder()->first()?->id ?? 1;
        $feedbackType2 = FeedbackType::inRandomOrder()->first()?->id ?? 1;

        return [
            'quote_id' => $quote->id,
            'customer_id' => $customer->id,
            'specialist_id' => $specialist->id,
            'status_id' => 1, // Default status is pending
            'override_quote' => $overrideQuote,
            'amount' => $overrideQuote ? $this->faker->randomFloat(2, 10, 1000) : null,
            'customer_feedback_id' => $feedbackType1,
            'customer_feedback' => $this->faker->text(200),
            'specialist_feedback_id' => $feedbackType2,
            'specialist_feedback' => $this->faker->text(200),
        ];
    }

    /**
     * Configure the factory to use an existing quote.
     *
     * @param Quote $quote The quote to use
     * @return $this
     */
    public function forQuote(Quote $quote)
    {
        return $this->state(function (array $attributes) use ($quote) {
            $listing = $quote->listing;

            // Get feedback types safely
            $feedbackType1 = FeedbackType::inRandomOrder()->first()?->id ?? 1;
            $feedbackType2 = FeedbackType::inRandomOrder()->first()?->id ?? 1;

            return [
                'quote_id' => $quote->id,
                'customer_id' => $listing->user_id,
                'specialist_id' => $quote->user_id,
                'customer_feedback_id' => $feedbackType1,
                'specialist_feedback_id' => $feedbackType2,
            ];
        });
    }
}