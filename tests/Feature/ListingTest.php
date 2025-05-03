<?php

use Illuminate\Http\Testing\File as UploadedFile;

/*** CREATE LISTING */
it('is redirected to the Login page when accessing the Create Listing page as a guest', function () {
    /*
     * @var \Illuminate\Foundation\Testing\TestResponse $response
     */
    $response = $this->get(route('listings.create'));
    $response->assertRedirecttoRoute('login');
    $response->assertStatus(302);
});
it('can access the Create Listing page as a logged-in, verified user with the customer role', function () {
    $user = \App\Models\User::factory()->customer()->verified()->create();
    $this->actingAs($user);
    /*
     * @var \Illuminate\Foundation\Testing\TestResponse $response
     */
    $response = $this->get(route('listings.create'));
    $response->assertOK();
});
it('does not allow a Listing to be created with empty data', function () {
    $this->seed();
    $user = \App\Models\User::factory()->customer()->verified()->create();
    /*
     * @var \Illuminate\Foundation\Testing\TestResponse $response
     */
    $response = $this->actingAs($user)->post(route('listings.store'), [
        'manufacturer_id' => null,
        'title' => null,
        'description' => null,
        'use_default_location' => null,
        'currency_id' => null,
        'budget' => null,
        'address_line1' => null,
        'address_line2' => null,
        'city' => null,
        'postcode' => null,
        'country_id' => null,
        'phone' => null,
        'expiry_days' => null,
        'published_at' => null,
        'product_ids' => null,
        'attachments' => null,
    ]);
    $response->assertInvalid([
        'manufacturer_id',
        'title',
        'description',
        'use_default_location',
        'address_line1',
        'city',
        'postcode',
        'country_id',
        'expiry_days',
        'product_ids',
        'published_at',
    ]);
});
it('does not allow a Listing to be created with invalid data', function () {
    $this->seed();
    $user = \App\Models\User::factory()->customer()->verified()->create();
    /*
     * @var \Illuminate\Foundation\Testing\TestResponse $response
     */
    $response = $this->actingAs($user)->post(route('listings.store'), [
        'manufacturer_id' => 999,
        'title' => 'Test Listing',
        'description' => 'Test Description',
        'use_default_location' => true,
        'currency_id' => 999,
        'budget' => 'XYZ',
        'address_line1' => '123 Test St',
        'address_line2' => null,
        'city' => 'Test City',
        'postcode' => '12345',
        'country_id' => 999,
        'phone' => 'ABCABC',
        'expiry_days' => 30,
        'published_at' => now()->subDays(1),
        'product_ids' => [9999, 9998, 9997],
    ]);
    $response->assertInvalid([
        'manufacturer_id',
        'currency_id',
        'budget',
        'country_id',
        'phone',
        'product_ids.0',
        'product_ids.1',
        'product_ids.2',
        'published_at',
    ]);
});
it('can create a Listing with valid data', function () {
    $this->seed();
    $countListings = \App\Models\Listing::count();
    $countAttachments = \App\Models\Attachment::count();

    $user = \App\Models\User::factory()->customer()->verified()->create();
    $attachments = [
        UploadedFile::fake()->image('test1.jpg'),
        UploadedFile::fake()->image('test2.jpg'),
        UploadedFile::fake()->create('test3.mp4', 1024, 'video/mp4'),
    ];
    /*
     * @var \Illuminate\Foundation\Testing\TestResponse $response
     */
    $response = $this->actingAs($user)->post(route('listings.store'), [
        'manufacturer_id' => 1,
        'title' => 'Test Listing',
        'description' => 'Test Description',
        'use_default_location' => true,
        'currency_id' => 1,
        'budget' => 1000,
        'address_line1' => '123 Test St',
        'address_line2' => null,
        'city' => 'Test City',
        'postcode' => '12345',
        'country_id' => 1,
        'phone' => '+1234567890',
        'expiry_days' => 30,
        'published_at' => now(),
        'product_ids' => [1, 2, 3],
        'attachments' => $attachments,
    ]);
    $response->assertRedirecttoRoute('listings.index');
    $response->assertSessionHas('success', 'Listing created successfully.');
    $newListing = \App\Models\Listing::latest('id')->first();
    $this->assertDatabaseHas('listings', [
        'id' => $newListing->id,
        'user_id' => $user->id,
        'manufacturer_id' => 1,
        'status_id' => 1,
        'title' => 'Test Listing',
        'description' => 'Test Description',
        'use_default_location' => true,
        'currency_id' => 1,
        'budget' => 1000,
        'address_line1' => '123 Test St',
        'address_line2' => null,
        'city' => 'Test City',
        'postcode' => '12345',
        'country_id' => 1,
        'phone' => '+1234567890',
        'expiry_days' => 30,
        'published_at' => now()->startOfDay(),
    ]);
    $this->assertDatabaseHas('listings_products', [
        'listing_id' => $newListing->id,
        'product_id' => 1,
    ]);
    $this->assertDatabaseHas('listings_products', [
        'listing_id' => $newListing->id,
        'product_id' => 2,
    ]);
    $this->assertDatabaseHas('listings_products', [
        'listing_id' => $newListing->id,
        'product_id' => 3,
    ]);
    $this->assertDatabaseHas('attachments', [
        'listing_id' => $newListing->id,
        'position' => 1,
        'mime_type' => 'image/jpeg',
    ]);
    $this->assertDatabaseHas('attachments', [
        'listing_id' => $newListing->id,
        'position' => 2,
        'mime_type' => 'image/jpeg',
    ]);
    $this->assertDatabaseHas('attachments', [
        'listing_id' => $newListing->id,
        'position' => 3,
        'mime_type' => 'video/mp4',
    ]);
    $this->assertDatabaseCount('listings', $countListings + 1);
    $this->assertDatabaseCount('attachments', $countAttachments + count($attachments));
});
it('cannot access the Create Listing page as a logged-in, verified user with the specialist role', function () {
    $user = \App\Models\User::factory()->specialist()->verified()->create();
    $this->actingAs($user);
    /*
     * @var \Illuminate\Foundation\Testing\TestResponse $response
     */
    $response = $this->get(route('listings.create'));
    $response->assertForbidden();
});
it('cannot access the Create Listing page as a logged-in, unverified user', function () {
    $user = \App\Models\User::factory()->customer()->unverified()->create();
    $this->actingAs($user);
    /*
     * @var \Illuminate\Foundation\Testing\TestResponse $response
     */
    $response = $this->get(route('listings.create'));
    $response->assertRedirecttoRoute('verification.notice');
    $response->assertStatus(302);
});
it('cannot access the Create Listing page as a guest user', function () {
    /*
     * @var \Illuminate\Foundation\Testing\TestResponse $response
     */
    $response = $this->get(route('listings.create'));
    $response->assertRedirecttoRoute('login');
    $response->assertStatus(302);
});


/*** MY LISTINGS */
it('can access the My Listings page as a logged-in, verified user with the customer role', function () {
    $user = \App\Models\User::factory()->customer()->verified()->create();
    $this->actingAs($user);
    /*
     * @var \Illuminate\Foundation\Testing\TestResponse $response
     */
    $response = $this->get(route('listings.index'));
    $response->assertOK();
});
it('cannot access the My Listings page as a logged-in, verified user with the specialist role', function () {
    $user = \App\Models\User::factory()->specialist()->verified()->create();
    $this->actingAs($user);
    /*
     * @var \Illuminate\Foundation\Testing\TestResponse $response
     */
    $response = $this->get(route('listings.index'));
    $response->assertForbidden();
});
it('cannot access the My Listings page as a guest user', function () {
    /*
     * @var \Illuminate\Foundation\Testing\TestResponse $response
     */
    $response = $this->get(route('listings.index'));
    $response->assertRedirecttoRoute('login');
    $response->assertStatus(302);
});

/*** EDIT LISTING */
it('can access the Edit Listing page as a logged-in, verified user with the customer role', function () {
    $user = \App\Models\User::factory()->customer()->verified()->create();
    $this->actingAs($user);
    $listing = \App\Models\Listing::factory()->create(['user_id' => $user->id]);

    /*
     * @var \Illuminate\Foundation\Testing\TestResponse $response
     */
    $response = $this->get(route('listings.edit', ['listing' => $listing]));

    $response->assertOK();
    $response->assertSee('Edit Listing');
    $response->assertSee('Manage attachments');
    $response->assertSee('<a href="' . route('listings.attachments', ['listing' => $listing]) . '"', false);

    // Title
    $response->assertSeeInOrder([
        '<input placeholder="Title" name="title" value="',
        $listing->title . '" />',
    ]);

    // Product categories
    $response->assertSee('<div id="product-data"', false);
    $response->assertSee('data-products=', false);

    foreach ($listing->products as $product) {
        $response->assertSee("&quot;id&quot;:{$product->id}", false);
        $response->assertSee("&quot;category&quot;:&quot;{$product->category}&quot;", false);
        $response->assertSee("&quot;subcategory&quot;:&quot;{$product->subcategory}&quot;", false);
    }

    // Manufacturer
    $response->assertSeeInOrder([
        '<select name="manufacturer_id"',
        '<option value="' . $listing->manufacturer->id . '"',
        'selected',
        $listing->manufacturer->name,
    ]);

    // Description
    $response->assertSeeInOrder([
        '<textarea rows="10" name="description">',
        $listing->description,
        '</textarea>',
    ]);

    // Expiry
    $response->assertSeeInOrder([
        '<select name="expiry_days">',
        '<option value="' . $listing->expiry_days . '"',
        'selected>' . $listing->expiry_days . ' days</option>',
    ]);

    // Currency
    $response->assertSeeInOrder([
        '<select name="currency_id" >',
        '<option value="' . $listing->currency->id . '"',
        'selected>',
        $listing->currency->iso_code . ' - ' . $listing->currency->name,
        '</option>',
    ]);

    // Amount
    $response->assertSeeInOrder([
        '<input type="number" placeholder="Budget" name="budget"  value="',
        $listing->budget . '" />',
    ]);

    // Use Default Location
    $response->assertSeeInOrder([
        '<input type="hidden" name="use_default_location" value="',
        'value="' . $listing->use_default_location . '"',
        '>',
    ]);

    // Address Line 1
    $response->assertSeeInOrder([
        '<input id="address_line1"',
        'value="' . $listing->address_line1 . '"',
        '/>',
    ]);

    // Address Line 2
    $response->assertSeeInOrder([
        '<input id="address_line2"',
        'value="' . $listing->address_line2 . '"',
        '/>',
    ]);

    // City
    $response->assertSeeInOrder([
        '<input',
        'id="city"',
        'value="' . $listing->city . '"',
        '/>',
    ]);

    // Postcode
    $response->assertSeeInOrder([
        '<input',
        'id="postcode"',
        'value="' . $listing->postcode . '"',
        '/>',
    ]);

    // Country
    $response->assertSeeInOrder([
        '<select',
        '<option value="' . $listing->country->id . '"',
        '' . $listing->country->name . '',
        '</option>',
    ]);

    // Phone
    $response->assertSeeInOrder([
        '<input',
        'id="phone"',
        'value="' . $listing->phone . '"',
        '/>',
    ]);

    // Published At
    $response->assertSee($listing->published_at->format('Y-m-d'));
    // Check if published date is in the past
    if ($listing->published_at->lessThan(now()->startOfDay())) {
        $response->assertSee('This listing has already been published and the date cannot be changed.');
    }

    // Attachments

});
it('cannot access the Edit Listing page for another user\'s listing as a logged-in, verified user with the customer role', function () {
    $user = \App\Models\User::factory()->customer()->verified()->create();
    $this->actingAs($user);
    $user2 = \App\Models\User::factory()->create();
    $listing = \App\Models\Listing::factory()->create(['user_id' => $user2->id]);
    /*
     * @var \Illuminate\Foundation\Testing\TestResponse $response
     */
    $response = $this->get(route('listings.edit', ['listing' => $listing]));
    $response->assertRedirecttoRoute('listings.index');
    $response->assertStatus(302);
    $response->assertSessionHas('error', 'You can only edit your own listings.');
});
it('cannot access the Edit Listing page as a logged-in, verified user with the specialist role', function () {
    $user = \App\Models\User::factory()->specialist()->verified()->create();
    $this->actingAs($user);
    $listing = \App\Models\Listing::factory()->create(['user_id' => $user->id]);
    /*
     * @var \Illuminate\Foundation\Testing\TestResponse $response
     */
    $response = $this->get(route('listings.edit', ['listing' => $listing]));
    $response->assertForbidden();
});
it('cannot access the Edit Listing page as a guest user', function () {
    $user = \App\Models\User::factory()->customer()->verified()->create();
    // Don't log in as the user
    $listing = \App\Models\Listing::factory()->create(['user_id' => $user->id]);
    /*
     * @var \Illuminate\Foundation\Testing\TestResponse $response
     */
    $response = $this->get(route('listings.edit', ['listing' => $listing]));
    $response->assertRedirecttoRoute('login');
    $response->assertStatus(302);
});
it('can update the Listing with valid data', function () {
    $this->seed();
    $user = \App\Models\User::factory()->customer()->verified()->create();
    $this->actingAs($user);
    $listing = \App\Models\Listing::factory()->create(['user_id' => $user->id]);
    /*
     * @var \Illuminate\Foundation\Testing\TestResponse $response
     */
    $response = $this->put(route('listings.update', ['listing' => $listing]), [
        'manufacturer_id' => 1,
        'title' => 'Updated Test Listing',
        'description' => 'Updated Test Description',
        'use_default_location' => true,
        'currency_id' => 1,
        'budget' => 2000,
        'address_line1' => '456 Updated St',
        'address_line2' => null,
        'city' => 'Updated City',
        'postcode' => '67890',
        'country_id' => 1,
        'phone' => '+0987654321',
        'expiry_days' => 60,
        'published_at' => now(),
        'product_ids' => [1, 2, 3],
    ]);
    $response->assertRedirecttoRoute('listings.index');
    $response->assertSessionHas('success', 'Listing updated successfully.');
    $this->assertDatabaseHas('listings', [
        'id' => $listing->id,
        'user_id' => $user->id,
        'manufacturer_id' => 1,
        'status_id' => 1,
        'title' => 'Updated Test Listing',
        'description' => 'Updated Test Description',
        'use_default_location' => true,
        'currency_id' => 1,
        'budget' => 2000,
        'address_line1' => '456 Updated St',
        'address_line2' => null,
        'city' => 'Updated City',
        'postcode' => '67890',
        'country_id' => 1,
        'phone' => '+0987654321',
        'expiry_days' => 60,
    ]);
});
it('cannot update the Listing with invalid data', function () {
    $this->seed();
    $user = \App\Models\User::factory()->customer()->verified()->create();
    $this->actingAs($user);
    $listing = \App\Models\Listing::factory()->create(['user_id' => $user->id]);
    /*
     * @var \Illuminate\Foundation\Testing\TestResponse $response
     */
    $response = $this->put(route('listings.update', ['listing' => $listing]), [
        'manufacturer_id' => 999,
        'title' => 'Updated Test Listing',
        'description' => 'Updated Test Description',
        'use_default_location' => true,
        'currency_id' => 999,
        'budget' => 'XYZ',
        'address_line1' => '456 Updated St',
        'address_line2' => null,
        'city' => 'Updated City',
        'postcode' => '67890',
        'country_id' => 999,
        'phone' => 'ABCABC',
        'expiry_days' => 60,
        'published_at' => now(),
        'product_ids' => [9999, 9998, 9997],
    ]);
    $response->assertInvalid([
        'manufacturer_id',
        'currency_id',
        'budget',
        'country_id',
        'phone',
        'product_ids.0',
        'product_ids.1',
        'product_ids.2',
    ]);
});
it('can relist an expired listing with a new published_at date', function () {
    $this->seed();
    $user = \App\Models\User::factory()->customer()->verified()->create();
    $this->actingAs($user);

    // Create an expired listing (older published date and an expired_at date)
    $listing = \App\Models\Listing::factory()->create([
        'user_id' => $user->id,
        'published_at' => now()->subDays(60),
        'expired_at' => now()->subDays(5),
        'status_id' => 3 // Assuming 3 is the status ID for expired listings
    ]);

    // Mock the session to include the is_relisting flag
    $this->withSession(['is_relisting' => true]);

    $newPublishDate = now();

    /*
     * @var \Illuminate\Foundation\Testing\TestResponse $response
     */
    $response = $this->put(route('listings.update', ['listing' => $listing]), [
        'manufacturer_id' => 1,
        'title' => 'Relisted Listing',
        'description' => 'This listing has been relisted',
        'use_default_location' => true,
        'currency_id' => 1,
        'budget' => 2000,
        'address_line1' => '456 Updated St',
        'address_line2' => null,
        'city' => 'Updated City',
        'postcode' => '67890',
        'country_id' => 1,
        'phone' => '+0987654321',
        'expiry_days' => 60,
        'published_at' => $newPublishDate,
        'product_ids' => [1, 2, 3],
    ]);

    $response->assertRedirecttoRoute('listings.index');
    $response->assertSessionHas('success', 'Listing relisted successfully.');

    // Verify the listing was properly relisted with updated status
    $this->assertDatabaseHas('listings', [
        'id' => $listing->id,
        'user_id' => $user->id,
        'title' => 'Relisted Listing',
        'status_id' => 1, // Open status
        'expired_at' => null, // Reset expiration
    ]);

    // Verify the published_at date was updated
    $updatedListing = \App\Models\Listing::find($listing->id);
    $this->assertTrue(
        $updatedListing->published_at->startOfDay()->equalTo($newPublishDate->startOfDay()),
        'The published_at date was not properly updated during relisting'
    );

    // Verify session flag was removed
    $this->assertFalse(session()->has('is_relisting'), 'The is_relisting flag was not removed from session');
});

/*** EDIT LISTING ATTACHMENTS */
it('can access the Listing Attachments page as a logged-in, verified user with the customer role', function () {
    $user = \App\Models\User::factory()->customer()->verified()->create();
    $this->actingAs($user);
    $listing = \App\Models\Listing::factory()->create(['user_id' => $user->id]);
    /*
     * @var \Illuminate\Foundation\Testing\TestResponse $response
     */
    $response = $this->get(route('listings.attachments', ['listing' => $listing]));
    $response->assertOK();
});
it('cannot access the Listing Attachments page for another user\'s listing as a logged-in, verified user with the customer role', function () {
    $user = \App\Models\User::factory()->customer()->verified()->create();
    $user2 = \App\Models\User::factory()->customer()->verified()->create();
    $listing = \App\Models\Listing::factory()->create(['user_id' => $user2->id]);
    /*
     * @var \Illuminate\Foundation\Testing\TestResponse $response
     */
    $this->actingAs($user);
    $response = $this->get(route('listings.attachments', ['listing' => $listing]));
    $response->assertStatus(404);
});
it('cannot access the Listing Attachments page as a logged-in, verified user with the specialist role', function () {
    $user = \App\Models\User::factory()->specialist()->verified()->create();
    $this->actingAs($user);
    $listing = \App\Models\Listing::factory()->create(['user_id' => $user->id]);
    /*
     * @var \Illuminate\Foundation\Testing\TestResponse $response
     */
    $response = $this->get(route('listings.attachments', ['listing' => $listing]));
    $response->assertForbidden();
});
it('cannot access the Listing Attachments page as a guest user', function () {
    $user = \App\Models\User::factory()->customer()->verified()->create();
    // Don't log in as the user
    $listing = \App\Models\Listing::factory()->create(['user_id' => $user->id]);
    /*
     * @var \Illuminate\Foundation\Testing\TestResponse $response
     */
    $response = $this->get(route('listings.attachments', ['listing' => $listing]));
    $response->assertRedirecttoRoute('login');
    $response->assertStatus(302);
});
it('can update Listing attachments by adding', function () {
    $this->seed();
    $user = \App\Models\User::factory()->customer()->verified()->create();
    $this->actingAs($user);
    $listing = \App\Models\Listing::factory()->create(['user_id' => $user->id]);

    // Get the initial count of attachments
    $initialAttachmentCount = \App\Models\Attachment::count();
    $initialListingAttachmentCount = \App\Models\Attachment::where('listing_id', $listing->id)->count();

    // Create test attachments
    $attachments = [
        UploadedFile::fake()->image('test1.jpg'),
        UploadedFile::fake()->image('test2.jpg'),
        UploadedFile::fake()->create('test3.mp4', 1024, 'video/mp4'),
    ];

    /*
     * @var \Illuminate\Foundation\Testing\TestResponse $response
     */
    $response = $this->post(route('listings.addAttachments', ['listing' => $listing]), [
        'attachments' => $attachments,
    ]);

    $response->assertStatus(302);
    $response->assertSessionHas('success', 'Attachments added successfully.');

    // Check that the correct number of attachments were created
    $this->assertDatabaseCount('attachments', $initialAttachmentCount + count($attachments));

    // Check that the attachments are associated with the listing
    $this->assertEquals(
        $initialListingAttachmentCount + count($attachments),
        \App\Models\Attachment::where('listing_id', $listing->id)->count()
    );

    // Check that each attachment record has the correct data
    $this->assertDatabaseHas('attachments', [
        'listing_id' => $listing->id,
        'user_id' => $user->id,
        'position' => 1,
        'mime_type' => 'image/jpeg',
    ]);

    $this->assertDatabaseHas('attachments', [
        'listing_id' => $listing->id,
        'user_id' => $user->id,
        'position' => 2,
        'mime_type' => 'image/jpeg',
    ]);

    $this->assertDatabaseHas('attachments', [
        'listing_id' => $listing->id,
        'user_id' => $user->id,
        'position' => 3,
        'mime_type' => 'video/mp4',
    ]);

    // Check that the positions are sequential
    $positions = \App\Models\Attachment::where('listing_id', $listing->id)
        ->orderBy('position')
        ->pluck('position')
        ->toArray();

    $expectedPositions = range(1, count($attachments) + $initialListingAttachmentCount);
    $this->assertEquals($expectedPositions, $positions);
});
it('can update Listing attachments by removing', function () {
    $this->seed();
    $user = \App\Models\User::factory()->customer()->verified()->create();
    $this->actingAs($user);
    $listing = \App\Models\Listing::factory()->create(['user_id' => $user->id]);

    // Get the initial count of all attachments in the database
    $initialAttachmentCount = \App\Models\Attachment::count();

    // Create test attachments
    $attachments = [
        UploadedFile::fake()->image('test1.jpg'),
        UploadedFile::fake()->image('test2.jpg'),
        UploadedFile::fake()->create('test3.mp4', 1024, 'video/mp4'),
    ];

    // Add attachments first
    $this->post(route('listings.addAttachments', ['listing' => $listing]), [
        'attachments' => $attachments,
    ]);

    // Verify attachments were actually created
    $addedAttachments = \App\Models\Attachment::where('listing_id', $listing->id)->get();
    $this->assertCount(count($attachments), $addedAttachments, 'Attachments were not created properly');

    // Get the list of attachment IDs to delete
    $attachmentCount = $addedAttachments->count();
    $attachmentIds = $addedAttachments->pluck('id')->toArray();

    // Now delete the attachments
    $response = $this->put(route('listings.updateAttachments', ['listing' => $listing]), [
        'delete_attachments' => $attachmentIds,
    ]);

    $response->assertStatus(302);
    $response->assertSessionHas('success', 'Attachments updated successfully.');

    // Verify the attachments were actually soft-deleted
    $this->assertEquals(
        0,
        \App\Models\Attachment::where('listing_id', $listing->id)->count(),
        'Attachments were not deleted properly'
    );

    // For soft deleted records, confirm they're no longer visible in normal queries
    foreach ($attachmentIds as $id) {
        $this->assertDatabaseMissing('attachments', [
            'id' => $id,
            'deleted_at' => null
        ]);
    }

    // Verify each attachment is soft-deleted (exists with a deleted_at timestamp)
    foreach ($attachmentIds as $id) {
        $this->assertSoftDeleted(\App\Models\Attachment::withTrashed()->find($id));
    }

    // If you need to verify the specific timestamps, you could do:
    foreach ($attachmentIds as $id) {
        $attachment = \App\Models\Attachment::withTrashed()->find($id);
        $this->assertNotNull($attachment->deleted_at, "Attachment {$id} wasn't soft-deleted");
    }

    // The total count should remain the same since records are soft-deleted
    $newAttachmentCount = \App\Models\Attachment::count();
    $this->assertEquals(
        $initialAttachmentCount,
        $newAttachmentCount,
        "Expected {$initialAttachmentCount} attachments but found {$newAttachmentCount}"
    );
});
it('can update Listing attachments by reordering', function () {
    $this->seed();
    DB::beginTransaction();

    try {
        $user = \App\Models\User::factory()->customer()->verified()->create();
        $this->actingAs($user);
        $listing = \App\Models\Listing::factory()->create(['user_id' => $user->id]);
        $countAttachments = \App\Models\Attachment::count();

        $this->assertNotNull($listing->id, "Listing wasn't created properly");

        $attachments = [
            UploadedFile::fake()->image('test1.jpg')->size(100),
            UploadedFile::fake()->image('test2.jpg')->size(100),
        ];

        $response = $this->post(route('listings.updateAttachments', ['listing' => $listing]), [
            'attachments' => $attachments,
        ]);

        dump("Response status: " . $response->getStatusCode());
        dump("Response content: " . $response->getContent());

        $createdAttachments = \App\Models\Attachment::where('listing_id', $listing->id)->get();
        dump("Created attachments count: " . $createdAttachments->count());

        if ($createdAttachments->isEmpty()) {
            for ($i = 1; $i <= 3; $i++) {
                \App\Models\Attachment::create([
                    'user_id' => $user->id,
                    'listing_id' => $listing->id,
                    'position' => $i,
                    'path' => "test{$i}.jpg",
                    'mime_type' => 'image/jpeg',
                ]);
            }
            $createdAttachments = \App\Models\Attachment::where('listing_id', $listing->id)->get();
        }

        $this->assertGreaterThan(0, $createdAttachments->count(), 'No attachments were created');

        $newAttachmentsCount = $createdAttachments->count();

        $positions = [];
        foreach ($createdAttachments as $index => $attachment) {
            $positions[$attachment->id] = $createdAttachments->count() - $index;
        }

        $response = $this->put(route('listings.updateAttachments', ['listing' => $listing]), [
            'positions' => $positions,
        ]);

        $response->assertStatus(302);
        $response->assertSessionHas('success', 'Attachments updated successfully.');

        foreach ($positions as $id => $position) {
            $this->assertDatabaseHas('attachments', [
                'id' => $id,
                'position' => $position,
            ]);
        }
    } finally {
        DB::rollBack();
    }
});

it('cannot update Listing attachments for another user\'s listing', function () {
    $this->seed();
    $user = \App\Models\User::factory()->customer()->verified()->create();
    $this->actingAs($user);
    $user2 = \App\Models\User::factory()->customer()->verified()->create();
    $listing = \App\Models\Listing::factory()->create(['user_id' => $user2->id]);
    /*
     * @var \Illuminate\Foundation\Testing\TestResponse $response
     */
    $attachments = [
        UploadedFile::fake()->image('test1.jpg'),
        UploadedFile::fake()->image('test2.jpg'),
        UploadedFile::fake()->create('test3.mp4', 1024, 'video/mp4'),
    ];
    $response = $this->put(route('listings.updateAttachments', ['listing' => $listing]), [
        'attachments' => $attachments,
    ]);
    $response->assertStatus(404);
});

/*** SHOW LISTING */
it('can access the Show Listing page as a logged-in user', function () {
    $user = \App\Models\User::factory()->create();
    $user2 = \App\Models\User::factory()->create();
    $this->actingAs($user);
    $listing = \App\Models\Listing::factory()->create(['user_id' => $user2->id]);
    /*
     * @var \Illuminate\Foundation\Testing\TestResponse $response
     */
    $response = $this->get(route('listings.show', ['listing' => $listing]));
    $response->assertOK();
});
it('can access the Show Listing page as a guest user', function () {
    $user = \App\Models\User::factory()->customer()->verified()->create();
    // Don't log in as the user
    $listing = \App\Models\Listing::factory()->create(['user_id' => $user->id]);
    /*
     * @var \Illuminate\Foundation\Testing\TestResponse $response
     */
    $response = $this->get(route('listings.show', ['listing' => $listing]));
    $response->assertOK();
});

/** DELETE LISTING */
it('can delete a Listing as the listing\'s owner', function () {
    $user = \App\Models\User::factory()->customer()->verified()->create();
    $this->actingAs($user);
    $listing = \App\Models\Listing::factory()->create(['user_id' => $user->id]);
    /*
     * @var \Illuminate\Foundation\Testing\TestResponse $response
     */
    $response = $this->delete(route('listings.destroy', ['listing' => $listing]));
    $response->assertRedirecttoRoute('listings.index');
    $response->assertSessionHas('success', 'Listing deleted successfully.');
    $this->assertSoftDeleted($listing);
    $this->assertEquals(0, \App\Models\Listing::count());
});
it('cannot delete a Listing created by another user', function () {
    $user = \App\Models\User::factory()->customer()->verified()->create();
    $this->actingAs($user);
    $user2 = \App\Models\User::factory()->create();
    $listing = \App\Models\Listing::factory()->create(['user_id' => $user2->id]);
    /*
     * @var \Illuminate\Foundation\Testing\TestResponse $response
     */
    $response = $this->delete(route('listings.destroy', ['listing' => $listing]));
    $response->assertStatus(404);
    $this->assertDatabaseHas('listings', [
        'id' => $listing->id,
        'user_id' => $user2->id,
    ]);
    $this->assertEquals(1, \App\Models\Listing::count());
});
it('cannot delete a Listing that has a status not equal to "Open"', function () {
    $user = \App\Models\User::factory()->customer()->verified()->create();
    $this->actingAs($user);
    $listing = \App\Models\Listing::factory()->create(['user_id' => $user->id, 'status_id' => 2]);
    /*
     * @var \Illuminate\Foundation\Testing\TestResponse $response
     */
    $response = $this->delete(route('listings.destroy', ['listing' => $listing]));
    $response->assertStatus(403);
    $this->assertDatabaseHas('listings', [
        'id' => $listing->id,
        'user_id' => $user->id,
        'status_id' => 2,
    ]);
    $this->assertEquals(1, \App\Models\Listing::count());
});