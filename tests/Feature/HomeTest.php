<?php

it('displays "There are no listings published yet." on Home page when there are no listings', function () {
    $response = $this->get(route('home'));
    $response->assertStatus(200);
    $response->assertSee('There are no listings published yet');
});
it('displays correct listing details on Home page when there are listings', function () {
    $this->seed();
    $activeListingsCount = \App\Models\Listing::active()
        ->count();

    // Make sure there are listings to test with
    expect($activeListingsCount)->toBeGreaterThan(0, 'No active listings were created by the seeder');

    $response = $this->get(route('home'));

    // Verify basic response
    $response->assertStatus(200);
    $response->assertDontSee('There are no listings published yet');

    // Check that the listings view variable contains the expected number of listings
    $response->assertViewHas('listings', function ($listings) use ($activeListingsCount) {
        return $listings->count() === $activeListingsCount;
    });

    // Verify the HTML content directly to ensure listings are actually rendered
    $actualListings = \App\Models\Listing::active()->get();
    foreach ($actualListings as $listing) {
        $response->assertSee($listing->title);
    }
});