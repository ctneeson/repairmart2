<?php

namespace Database\Factories;

use Illuminate\Database\Eloquent\Factories\Factory;
use App\Models\Listing;
use App\Models\Quote;
use App\Models\Order;
use App\Models\Email;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\Attachment>
 */
class AttachmentFactory extends Factory
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
        $emailId = null;

        $random = $this->faker->randomFloat(2, 0, 1);

        if ($random <= 0.50) {
            $listing = Listing::inRandomOrder()->first();
            $listingId = $listing ? $listing->id : null;
        } elseif ($random <= 0.65) {
            $quote = Quote::inRandomOrder()->first();
            $quoteId = $quote ? $quote->id : null;
        } elseif ($random <= 0.8) {
            $order = Order::inRandomOrder()->first();
            $orderId = $order ? $order->id : null;
        } else {
            $email = Email::inRandomOrder()->first();
            $emailId = $email ? $email->id : null;
        }

        return [
            'user_id' => null, // Default value, will be overridden
            'listing_id' => $listingId,
            'quote_id' => $quoteId,
            'order_id' => $orderId,
            'email_id' => $emailId,
            'position' => 1, // Default value, will be overridden
            'path' => 'attachments/no-photo-available.jpg',
            'mime_type' => 'image/jpeg',
        ];
    }

    public function forListing(Listing $listing)
    {
        return $this->state([
            'listing_id' => $listing->id,
            'user_id' => $listing->user_id, // Use the listing's owner
        ]);
    }
}