<?php

namespace Database\Factories;

use Illuminate\Database\Eloquent\Factories\Factory;
use App\Models\Listing;
use App\Models\Quote;
use App\Models\Order;
use App\Models\User;
use Illuminate\Support\Facades\DB;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\Email>
 */
class EmailFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        $listingId = null;
        $quoteId = null;
        $orderId = null;
        $fromId = null;
        $subject = null;

        $random = $this->faker->randomFloat(2, 0, 1);

        if ($random <= 0.40) {
            $listingId = Listing::inRandomOrder()->first()->id;
            $subject = "Re: " . $listingId;
        } elseif ($random <= 0.70) {
            $quoteId = Quote::inRandomOrder()->first()->id;
            $subject = "Re: " . $quoteId;
        } else {
            $orderId = Order::inRandomOrder()->first()->id;
            $subject = "Re: " . $orderId;
        }

        if ($listingId) {
            $randomFrom = $this->faker->randomFloat(2, 0, 1);
            if ($randomFrom <= 0.50) {
                $fromId = User::where('email', 'system@repairmart.net')->first()->id;
            } elseif ($randomFrom <= 0.75) {
                $fromId = Listing::find($listingId)->user_id;
            } else {
                $fromId = User::where('id', '!=', User::where('email', 'system@repairmart.net')->first()->id)
                    ->where('id', '!=', Listing::find($listingId)->user_id)
                    ->inRandomOrder()->first()->id;
            }
        } elseif ($quoteId) {
            $randomFrom = $this->faker->randomFloat(2, 0, 1);
            if ($randomFrom <= 0.20) {
                $fromId = User::where('email', 'system@repairmart.net')->first()->id;
            } elseif ($randomFrom <= 0.60) {
                $fromId = Quote::find($quoteId)->user_id;
            } else {
                $fromId = User::where('id', '!=', User::where('email', 'system@repairmart.net')->first()->id)
                    ->where('id', '!=', Quote::find($quoteId)->user_id)
                    ->inRandomOrder()->first()->id;
            }
        } elseif ($orderId) {
            $randomFrom = $this->faker->randomFloat(2, 0, 1);
            if ($randomFrom <= 0.40) {
                $fromId = User::where('email', 'system@repairmart.net')->first()->id;
            } elseif ($randomFrom <= 0.70) {
                $quoteIdForOrder = Order::find($orderId)->quote_id;
                $fromId = Quote::find($quoteIdForOrder)->user_id;
            } else {
                $listingIdForOrder = Order::find($orderId)->listing_id;
                $fromId = Listing::find($listingIdForOrder)->user_id;
            }
        }

        return [
            'from_id' => $fromId,
            'listing_id' => $listingId,
            'quote_id' => $quoteId,
            'order_id' => $orderId,
            'subject' => $subject,
            'content' => $this->faker->paragraph,
        ];
    }
}