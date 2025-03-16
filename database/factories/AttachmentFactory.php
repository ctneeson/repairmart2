<?php

namespace Database\Factories;

use Illuminate\Database\Eloquent\Factories\Factory;
use App\Models\Listing;
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
        $orderId = null;
        $emailId = null;

        $random = $this->faker->randomFloat(2, 0, 1);

        if ($random <= 0.50) {
            $listingId = Listing::inRandomOrder()->first()->id;
        } elseif ($random <= 0.65) {
            $orderId = Order::inRandomOrder()->first()->id;
        } else {
            $emailId = Email::inRandomOrder()->first()->id;
        }

        return [
            'listing_id' => $listingId,
            'order_id' => $orderId,
            'email_id' => $emailId,
            'path' => $this->faker->imageUrl(),
        ];
    }
}