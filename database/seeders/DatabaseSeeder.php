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

        $this->call(CountriesSeeder::class);
        $this->call(CurrenciesSeeder::class);
        $this->call(DeliveryMethodsSeeder::class);
        $this->call(FeedbackTypesSeeder::class);
        $this->call(ListingStatusesSeeder::class);
        $this->call(ManufacturersSeeder::class);
        $this->call(OrderStatusesSeeder::class);
        $this->call(ProductsSeeder::class);
        $this->call(QuoteStatusesSeeder::class);

        // Create system user
        $systemUser = User::factory()->create([
            'name' => 'RepairMart',
            'email' => 'system@repairmart.net',
        ]);

        // Create a user with 5 listings
        $sidUser = User::factory()
            ->has(
                Listing::factory()
                    ->count(5)
                    ->has(
                        Attachment::factory()->count(2)->state(new Sequence(
                            ['position' => 1],
                            ['position' => 2]
                        )),
                        'attachments'
                    ), // Create 2 attachments for each listing with alternating positions
                'listingsCreated'
            )
            ->create([
                'email' => 'sid@penguins.com',
            ]);

        // Get all product IDs
        $productIds = Product::pluck('id')->toArray();

        // Attach 2 existing products to each listing
        foreach ($sidUser->listingsCreated as $listing) {
            $randomProductIds = array_rand($productIds, 2);
            $listing->products()->attach([
                $productIds[$randomProductIds[0]],
                $productIds[$randomProductIds[1]]
            ]);

            // Create an email from the system user to the user for each listing
            $email = Email::create([
                'from_id' => $systemUser->id,
                'subject' => 'Re: Listing ' . $listing->id,
                'content' => 'This is a notification regarding your listing.',
            ]);

            // Create an entry in the emails_recipients table
            $email->recipients()->attach($sidUser->id);
        }

        // Create a new user with email connor@oilers.ca
        $connorUser = User::factory()->create([
            'email' => 'connor@oilers.ca',
        ]);

        // Generate 2 quotes for each listing created for the user with email sid@penguins.com
        foreach ($sidUser->listingsCreated as $listing) {
            $currencyId = $listing->budget_currency_id;
            $amount = $listing->budget;

            // Create the first quote with deliverymethod_id = 2
            Quote::create([
                'user_id' => $connorUser->id,
                'listing_id' => $listing->id,
                'status_id' => 1,
                'currency_id' => $currencyId,
                'deliverymethod_id' => 2,
                'turnaround' => 10,
                'use_default_location' => true,
                'amount' => $amount,
            ]);

            // Create the second quote with deliverymethod_id = 3 and amount 10 units greater
            Quote::create([
                'user_id' => $connorUser->id,
                'listing_id' => $listing->id,
                'status_id' => 1,
                'currency_id' => $currencyId,
                'deliverymethod_id' => 3,
                'turnaround' => 15,
                'use_default_location' => true,
                'amount' => $amount + 10,
            ]);
        }
    }
}