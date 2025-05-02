<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Database\Eloquent\Factories\Sequence;
use App\Models\User;
use App\Models\Product;
use App\Models\Listing;
use App\Models\Attachment;
use App\Models\Email;
use App\Models\Quote;

class DatabaseSeeder extends Seeder
{
    /**
     * Seed the application's database.
     */
    public function run(): void
    {
        // User::factory(10)->create();

        // User::factory()->create([
        //     'name' => 'Test User',
        //     'email' => 'test@example.com',
        // ]);

        // Create system user
        $systemUser = User::factory()
            ->admin()
            ->customer()
            ->specialist()
            ->verified()
            ->create([
                'name' => 'RepairMart',
                'email' => 'system@repairmart.net',
            ]);

        // Create a user with 5 listings
        $customer = User::factory()
            ->customer()
            ->verified()
            ->has(
                Listing::factory()
                    ->count(5)
                    ->afterCreating(function (Listing $listing) {
                        // Create attachments with the correct user_id
                        Attachment::factory()
                            ->forListing($listing)
                            ->count(2)
                            ->state(new Sequence(
                                ['position' => 1],
                                ['position' => 2]
                            ))
                            ->create();
                    }),
                'listingsCreated'
            )
            ->create([
                'email' => 'customer@customer.com',
            ]);

        // Get all product IDs
        $productIds = Product::pluck('id')->toArray();

        // Attach 2 existing products to each listing
        foreach ($customer->listingsCreated as $listing) {
            $randomProductIds = array_rand($productIds, 2);
            $listing->products()->attach([
                $productIds[$randomProductIds[0]],
                $productIds[$randomProductIds[1]]
            ]);

            // Create an email from the system user to the user for each listing
            $email = Email::create([
                'sender_id' => $systemUser->id,
                'subject' => 'Re: Listing ' . $listing->id,
                'content' => 'This is a notification regarding your listing.',
            ]);

            // Create an entry in the emails_recipients table
            $email->recipients()->attach($customer->id);
        }

        // Create a new specialist user
        // with 2 quotes for each listing created for the customer user
        $specialist = User::factory()
            ->specialist()
            ->verified()
            ->create([
                'email' => 'specialist@specialist.com',
            ]);

        // Generate 2 quotes for each listing created for the customer user
        foreach ($customer->listingsCreated as $listing) {
            $currencyId = $listing->currency_id;
            $amount = $listing->budget;

            // Create the first quote with deliverymethod_id = 2
            Quote::create([
                'user_id' => $specialist->id,
                'listing_id' => $listing->id,
                'status_id' => 1,
                'currency_id' => $currencyId,
                'deliverymethod_id' => 2,
                'turnaround' => 10,
                'use_default_location' => true,
                'address_line1' => $specialist->address_line1,
                'address_line2' => $specialist->address_line2,
                'city' => $specialist->city,
                'postcode' => $specialist->postcode,
                'country_id' => $specialist->country_id,
                'amount' => $amount,
            ]);

            // Create the second quote with deliverymethod_id = 3 and amount 10 units greater
            Quote::create([
                'user_id' => $specialist->id,
                'listing_id' => $listing->id,
                'status_id' => 1,
                'currency_id' => $currencyId,
                'deliverymethod_id' => 3,
                'turnaround' => 15,
                'use_default_location' => true,
                'address_line1' => $specialist->address_line1,
                'address_line2' => $specialist->address_line2,
                'city' => $specialist->city,
                'postcode' => $specialist->postcode,
                'country_id' => $specialist->country_id,
                'amount' => $amount + 10,
            ]);
        }
    }
}