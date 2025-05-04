<?php

use Illuminate\Http\UploadedFile;

/*** CREATE QUOTE */
it('redirects to the login page when accessing the Create Quote page as a guest user', function () {
    $user = \App\Models\User::factory()->customer()->verified()->create();
    $listing = \App\Models\Listing::factory()->create(['user_id' => $user->id]);
    /*
     * @var \Illuminate\Foundation\Testing\TestResponse $response
     */
    $response = $this->get(route('quotes.create', ['listing' => $listing]));
    $response->assertRedirecttoRoute('login');
    $response->assertStatus(302);
});
it('cannot access the Create Quote page as a logged-in, verified user with the customer role', function () {
    $customer = \App\Models\User::factory()->customer()->verified()->create();
    $listing = \App\Models\Listing::factory()->create(['user_id' => $customer->id]);
    $user = \App\Models\User::factory()->customer()->verified()->create();
    /*
     * @var \Illuminate\Foundation\Testing\TestResponse $response
     */
    $this->actingAs($user);
    $response = $this->get(route('quotes.create', ['listing' => $listing]));
    $response->assertRedirecttoRoute('listings.show', ['listing' => $listing]);
    $response->assertStatus(302);
    $response->assertSessionHas('error', 'You must have an address and be a specialist or admin to create quotes.');
});
it('cannot access the Create Quote page as the listing owner', function () {
    $user = \App\Models\User::factory()->customer()->specialist()->verified()->create();
    $listing = \App\Models\Listing::factory()->create(['user_id' => $user->id]);
    $this->actingAs($user);
    /*
     * @var \Illuminate\Foundation\Testing\TestResponse $response
     */
    $response = $this->get(route('quotes.create', ['listing' => $listing]));
    $response->assertRedirecttoRoute('listings.show', ['listing' => $listing]);
    $response->assertStatus(302);
    $response->assertSessionHas('error', 'You cannot create a quote for your own listing.');
});
it('can access the Create Quote page as a logged-in, verified user with the specialist role', function () {
    $customer = \App\Models\User::factory()->customer()->verified()->create();
    $listing = \App\Models\Listing::factory()->create(['user_id' => $customer->id]);
    $specialist = \App\Models\User::factory()->specialist()->verified()->create();
    $this->actingAs($specialist);
    /*
     * @var \Illuminate\Foundation\Testing\TestResponse $response
     */
    $response = $this->get(route('quotes.create', ['listing' => $listing]));
    $response->assertOK();
    $response->assertSeeInOrder([
        'Create a Quote',
        'Listing Details',
        $listing->title,
        '<a href="' . route('listings.show', ['listing' => $listing]) . '"',
        'View Listing',
        '</a>',
        '<label>Manufacturer</label>',
        '<div class="detail-value">',
        $listing->manufacturer->name,
        '</div>',
        '<label>Budget</label>',
        '<div class="detail-value">',
        $listing->currency->iso_code . ' ' . number_format($listing->budget, 2),
        '</div>',
        '<label>Expiry</label>',
        '<div class="detail-value">',
        $listing->created_at->addDays($listing->expiry)->format('Y-m-d'),
        '</div>',
        '<label>Description</label>',
        '<div class="detail-value">',
        $listing->description,
        '</div>',
        '<span>' . $listing->customer->name . '</span>',
        '<span>',
        $listing->address_line1,
        !empty($listing->address_line2) ? ', ' . $listing->address_line2 : '',
        $listing->city . ', ' . $listing->country->name,
        $listing->postcode,
        !empty($listing->phone) ? $listing->phone : '',
        '</span>',
        'Quote Details',
        '<form action="' . route('quotes.store') . '"',
        '<select name="currency_id" name="currency_id">',
        '<option value="">Select a currency</option>',
        '<label for="amount">Amount</label>',
        '<input type="number"',
        'name="amount"',
        'id="amount"',
        '<select name="deliverymethod_id" id="deliverymethod_id" required>',
        '<label for="turnaround">Turnaround (Days)</label>',
        '<input type="number"',
        'name="turnaround"',
        'id="turnaround"',
        '<label for="description">Quote Description</label>',
        '<textarea',
        'name="description"',
        'id="description"',
        '<input',
        'type="checkbox"',
        'name="use_default_location"',
        'value="1"',
        '<label for="use-default-location">Use My Default Address</label>',
        '<input',
        'type="text"',
        'name="address_line1"',
        'id="address_line1"',
        $specialist->address_line1,
        '<input',
        'type="text"',
        'name="address_line2"',
        'id="address_line2"',
        !empty($specialist->address_line2) ? $specialist->address_line2 : '',
        '<input',
        'type="text"',
        'name="city"',
        'id="city"',
        $specialist->city,
        '<input',
        'type="text"',
        'name="postcode"',
        'id="postcode"',
        $specialist->postcode,
        '<select',
        'name="_country_id"',
        'id="country_id"',
        '<option value="' . $specialist->country->id . '" selected>',
        $specialist->country->name,
        '</option>',
        '</select>',
        '<input',
        'type="text"',
        'name="phone"',
        'id="phone"',
        '<button',
        'Cancel',
        '</button>',
        '<button',
        'Reset',
        '</button>',
        '<button',
        'Submit',
        '</button>',
    ]);
    // Check for product categories separately
    foreach ($listing->products as $product) {
        $response->assertSee($product->category . ' > ' . $product->subcategory);
    }
});


/*** VIEW QUOTE */
it('redirects to the login page when accessing the View Quote page as a guest user', function () {
    /*
     * @var \Illuminate\Foundation\Testing\TestResponse $response
     */
    $response = $this->get(route('quotes.show', ['quote' => 1]));
    $response->assertRedirecttoRoute('login');
    $response->assertStatus(302);
});
it('cannot access the View Quote page as a user who is not the quote creator or customer', function () {
    $user = \App\Models\User::factory()->customer()->verified()->create();
    $user2 = \App\Models\User::factory()->specialist()->verified()->create();
    $user3 = \App\Models\User::factory()->specialist()->verified()->create();
    $listing = \App\Models\Listing::factory()->create(['user_id' => $user->id]);
    $quote = \App\Models\Quote::factory()->create(['listing_id' => $listing->id, 'user_id' => $user2->id]);
    /*
     * @var \Illuminate\Foundation\Testing\TestResponse $response
     */
    $this->actingAs($user3);
    $response = $this->get(route('quotes.show', ['quote' => $quote]));
    $response->assertRedirecttoRoute('home');
    $response->assertStatus(302);
});
it('can access the View Quote page as the user who created the quote', function () {
    $customer = \App\Models\User::factory()->customer()->verified()->create();
    $specialist = \App\Models\User::factory()->specialist()->verified()->create();
    $listing = \App\Models\Listing::factory()->create(['user_id' => $customer->id]);
    $quote = \App\Models\Quote::factory()->create(['listing_id' => $listing->id, 'user_id' => $specialist->id]);
    /*
     * @var \Illuminate\Foundation\Testing\TestResponse $response
     */
    $this->actingAs($specialist);
    $response = $this->get(route('quotes.show', ['quote' => $quote]));
    $response->assertOK();
    $response->assertSeeInOrder([
        'Quote #' . $quote->id,
        $quote->status->name,
        'Quote Details',
        'Amount',
        $quote->currency->iso_code . ' ' . number_format($quote->amount, 2),
        'Turnaround Time',
        $quote->turnaround . ' days',
        'Delivery Method',
        $quote->deliveryMethod->name,
        'Created On',
        $quote->created_at->format('d M Y, H:i'),
        'Quote Description',
        $quote->description,
        'Listing Details',
        $quote->listing->title,
        '<a href="' . route('listings.show', ['listing' => $quote->listing]) . '"',
        'view',
        '</a>',
        'Manufacturer',
        $quote->listing->manufacturer->name,
        'Product(s)',
        'Specialist',
        '<a href="' . route('profile.show', ['user' => $quote->repairspecialist]) . '"',
        $quote->repairspecialist->name,
        '</a>',
        $quote->address_line1,
        !empty($quote->address_line2) ? ', ' . $quote->address_line2 : '',
        !empty($quote->phone) ? ', ' . $quote->phone : '',
        'Customer',
        '<a href="' . route('profile.show', ['user' => $quote->customer]) . '"',
        $quote->customer->name,
        '</a>',
        $quote->listing->address_line1,
        !empty($quote->listing->address_line2) ? ', ' . $quote->listing->address_line2 : '',
        $quote->listing->city . ', ' . $quote->listing->country->name,
        $quote->listing->postcode,
        !empty($quote->listing->phone) ? $quote->listing->phone : '',
        '<a href="' . route('quotes.edit', $quote->id) . '"',
        'Edit Quote',
        '</a>',
    ]);
    foreach ($quote->listing->products as $product) {
        $response->assertSee($product->category . ' > ' . $product->subcategory);
    }
});
it('can access the View Quote page as the quote customer', function () {
    $user = \App\Models\User::factory()->customer()->verified()->create();
    $this->actingAs($user);
    $listing = \App\Models\Listing::factory()->create(['user_id' => $user->id]);
    $quote = \App\Models\Quote::factory()->create(['listing_id' => $listing->id]);
    /*
     * @var \Illuminate\Foundation\Testing\TestResponse $response
     */
    $response = $this->get(route('quotes.show', ['quote' => $quote]));
    $response->assertOK();
    $response->assertSeeInOrder([
        'Quote #' . $quote->id,
        $quote->status->name,
        'Quote Details',
        'Amount',
        $quote->currency->iso_code . ' ' . number_format($quote->amount, 2),
        'Turnaround Time',
        $quote->turnaround . ' days',
        'Delivery Method',
        $quote->deliveryMethod->name,
        'Created On',
        $quote->created_at->format('d M Y, H:i'),
        'Quote Description',
        $quote->description,
        'Listing Details',
        $quote->listing->title,
        '<a href="' . route('listings.show', ['listing' => $quote->listing]) . '"',
        'view',
        '</a>',
        'Manufacturer',
        $quote->listing->manufacturer->name,
        'Product(s)',
        'Specialist',
        '<a href="' . route('profile.show', ['user' => $quote->repairspecialist]) . '"',
        $quote->repairspecialist->name,
        '</a>',
        $quote->address_line1,
        !empty($quote->address_line2) ? ', ' . $quote->address_line2 : '',
        !empty($quote->phone) ? ', ' . $quote->phone : '',
        'Customer',
        '<a href="' . route('profile.show', ['user' => $quote->customer]) . '"',
        $quote->customer->name,
        '</a>',
        $quote->listing->address_line1,
        !empty($quote->listing->address_line2) ? ', ' . $quote->listing->address_line2 : '',
        $quote->listing->city . ', ' . $quote->listing->country->name,
        $quote->listing->postcode,
        !empty($quote->listing->phone) ? $quote->listing->phone : '',
        '<a ',
        'href="' . route('email.create', ['quote_id' => $quote->id, 'recipient_ids[]' => $quote->user_id]) . '"',
        'Contact Repair Specialist',
        '</a>',
        '<a href="' . route('orders.create', $quote) . '"',
        'Accept Quote',
        '</a>',
    ]);
    foreach ($quote->listing->products as $product) {
        $response->assertSee($product->category . ' > ' . $product->subcategory);
    }
});

/*** EDIT QUOTE */
it('redirects to the login page when accessing the Edit Quote page as a guest user', function () {
    /*
     * @var \Illuminate\Foundation\Testing\TestResponse $response
     */
    $response = $this->get(route('quotes.edit', ['quote' => 1]));
    $response->assertRedirecttoRoute('login');
    $response->assertStatus(302);
});
it('cannot access the Edit Quote page as the quote customer', function () {
    $customer = \App\Models\User::factory()->customer()->verified()->create();
    $specialist = \App\Models\User::factory()->specialist()->verified()->create();
    $listing = \App\Models\Listing::factory()->create(['user_id' => $customer->id]);
    $quote = \App\Models\Quote::factory()->create(['listing_id' => $listing->id, 'user_id' => $specialist->id]);
    /*
     * @var \Illuminate\Foundation\Testing\TestResponse $response
     */
    $this->actingAs($customer);
    $response = $this->get(route('quotes.edit', ['quote' => $quote]));
    $response->assertRedirecttoRoute('quotes.show', ['quote' => $quote]);
    $response->assertStatus(302);
});
it('cannot access the Edit Quote page as a user other than the one who created the quote', function () {
    $user = \App\Models\User::factory()->customer()->verified()->create();
    $user2 = \App\Models\User::factory()->specialist()->verified()->create();
    $user3 = \App\Models\User::factory()->specialist()->verified()->create();
    $listing = \App\Models\Listing::factory()->create(['user_id' => $user->id]);
    $quote = \App\Models\Quote::factory()->create(['listing_id' => $listing->id, 'user_id' => $user2->id]);
    /*
     * @var \Illuminate\Foundation\Testing\TestResponse $response
     */
    $this->actingAs($user3);
    $response = $this->get(route('quotes.edit', ['quote' => $quote]));
    $response->assertRedirecttoRoute('quotes.show', ['quote' => $quote]);
    $response->assertStatus(302);
});
it('can access the Edit Quote page as the user who created the quote', function () {
    $customer = \App\Models\User::factory()->customer()->verified()->create();
    $listing = \App\Models\Listing::factory()->create(['user_id' => $customer->id]);
    $specialist = \App\Models\User::factory()->specialist()->verified()->create();
    $quote = \App\Models\Quote::factory()->create(['listing_id' => $listing->id, 'user_id' => $specialist->id]);
    /*
     * @var \Illuminate\Foundation\Testing\TestResponse $response
     */
    $this->actingAs($specialist);
    $response = $this->get(route('quotes.edit', ['quote' => $quote]));
    $response->assertOK();
    $response->assertSeeInOrder([
        'Edit Quote #' . $quote->id,
        'Listing Details',
        $quote->listing->title,
        '<a href="' . route('listings.show', ['listing' => $quote->listing]) . '"',
        'View Listing',
        '</a>',
        '<label>Product Classification</label>',
        '<label>Manufacturer</label>',
        $quote->listing->manufacturer->name,
        '<label>Budget</label>',
        $quote->listing->currency->iso_code . ' ' . number_format($quote->listing->budget, 2),
        '<label>Expiry</label>',
        $quote->listing->created_at->addDays($quote->listing->expiry)->format('Y-m-d'),
        '<label>Description</label>',
        $quote->listing->description,
        'Customer',
        '<span>' . $quote->customer->name . '</span>',
        '<span>',
        $quote->listing->address_line1,
        !empty($quote->listing->address_line2) ? ', ' . $quote->listing->address_line2 : '',
        $quote->listing->city . ', ' . $quote->listing->country->name,
        $quote->listing->postcode,
        !empty($quote->listing->phone) ? $quote->listing->phone : '',
        '</span>',
        'Quote Details',
        '<form',
        'action="' . route('quotes.update', ['quote' => $quote]) . '"',
        '<select name="currency_id" name="currency_id">',
        '<option value="' . $quote->currency->id . '" selected>',
        $quote->currency->iso_code . ' - ' . $quote->currency->name,
        '</option>',
        '<label for="amount">Amount</label>',
        '<input type="number"',
        'name="amount"',
        'id="amount"',
        'value="' . $quote->amount . '"',
        '<select name="deliverymethod_id" id="deliverymethod_id" required>',
        '<option value="' . $quote->deliverymethod_id . '" selected>',
        $quote->deliveryMethod->name,
        '</option>',
        '<label for="turnaround">Turnaround (Days)</label>',
        '<input type="number"',
        'name="turnaround"',
        'id="turnaround"',
        'value="' . $quote->turnaround . '"',
        '<label for="description">Quote Description</label>',
        '<textarea',
        'name="description"',
        'id="description"',
        $quote->description,
        '</textarea>',
        '<input',
        'type="checkbox"',
        'name="use_default_location"',
        'value="',
        $quote->use_default_location ? '1' : '0',
        '"',
        $quote->use_default_location ? 'checked' : '',
        '<label for="use-default-location">Use My Default Address</label>',
        '<input',
        'type="text"',
        'name="address_line1"',
        'id="address_line1"',
        'value="' . $quote->address_line1 . '"',
        '<input',
        'type="text"',
        'name="address_line2"',
        'id="address_line2"',
        'value="' . $quote->address_line2 . '"',
        '<input',
        'id="city"',
        'name="city"',
        'type="text"',
        'value="' . $quote->city . '"',
        '<input',
        'type="text"',
        'name="postcode"',
        'id="postcode"',
        'value="' . $quote->postcode . '"',
        '<select',
        'id="country_id"',
        '<option value="' . $quote->country->id . '" selected>',
        $quote->country->name,
        '</option>',
        '</select>',
        '<input',
        'type="text"',
        'name="phone"',
        'id="phone"',
        'value="' . $quote->phone . '"',
        '<a href="' . route('quotes.show', ['quote' => $quote]) . '"',
        'Cancel',
        '</a>',
        '<button',
        'Save Changes',
        '</button>',
        '<button',
        'Delete Quote',
        '</button>',
    ]);
    foreach ($quote->listing->products as $product) {
        $response->assertSee($product->category . ' > ' . $product->subcategory);
    }

});

/*** EDIT QUOTE ATTACHMENTS */
it('cannot access Quote Attachments as a guest user', function () {
    $customer = \App\Models\User::factory()->customer()->verified()->create();
    $listing = \App\Models\Listing::factory()->create(['user_id' => $customer->id]);
    $specialist = \App\Models\User::factory()->specialist()->verified()->create();
    $quote = \App\Models\Quote::factory()->create(['listing_id' => $listing->id, 'user_id' => $specialist->id]);
    /*
     * @var \Illuminate\Foundation\Testing\TestResponse $response
     */
    $response = $this->get(route('quotes.attachments', ['quote' => $quote]));
    $response->assertRedirecttoRoute('login');
    $response->assertStatus(302);
});
it('cannot access Quote Attachments as a user other than the one who created the quote', function () {
    $user = \App\Models\User::factory()->customer()->verified()->create();
    $user2 = \App\Models\User::factory()->specialist()->verified()->create();
    $user3 = \App\Models\User::factory()->specialist()->verified()->create();
    $listing = \App\Models\Listing::factory()->create(['user_id' => $user->id]);
    $quote = \App\Models\Quote::factory()->create(['listing_id' => $listing->id, 'user_id' => $user2->id]);
    /*
     * @var \Illuminate\Foundation\Testing\TestResponse $response
     */
    $this->actingAs($user3);
    $response = $this->get(route('quotes.attachments', ['quote' => $quote]));
    $response->assertRedirecttoRoute('quotes.show', ['quote' => $quote]);
    $response->assertStatus(302);
});
it('can access Quote Attachments as the user who created the quote', function () {
    $customer = \App\Models\User::factory()->customer()->verified()->create();
    $listing = \App\Models\Listing::factory()->create(['user_id' => $customer->id]);
    $specialist = \App\Models\User::factory()->specialist()->verified()->create();
    $quote = \App\Models\Quote::factory()->create(['listing_id' => $listing->id, 'user_id' => $specialist->id]);
    $user = \App\Models\User::factory()->customer()->verified()->create();
    /*
     * @var \Illuminate\Foundation\Testing\TestResponse $response
     */
    $this->actingAs($specialist);
    $response = $this->get(route('quotes.attachments', ['quote' => $quote]));
    $response->assertOK();
});
it('can update Quote Attachments by adding', function () {
    $this->seed();
    $customer = \App\Models\User::factory()->customer()->verified()->create();
    $specialist = \App\Models\User::factory()->specialist()->verified()->create();
    $listing = \App\Models\Listing::factory()->create(['user_id' => $customer->id]);
    $quote = \App\Models\Quote::factory()->create(['listing_id' => $listing->id, 'user_id' => $specialist->id]);
    $this->actingAs($specialist);

    $initialAttachmentCount = \App\Models\Attachment::count();
    $initialQuoteAttachmentCount = \App\Models\Attachment::where('quote_id', $quote->id)->count();

    $attachments = [
        UploadedFile::fake()->image('test1.jpg'),
        UploadedFile::fake()->image('test2.jpg'),
        UploadedFile::fake()->create('test3.mp4', 1024, 'video/mp4'),
    ];

    /*
     * @var \Illuminate\Foundation\Testing\TestResponse $response
     */
    $response = $this->post(route('quotes.addAttachments', ['quote' => $quote]), [
        'attachments' => $attachments,
    ]);
    $response->assertStatus(302);
    $response->assertSessionHas('success', 'Attachments added successfully.');
    $this->assertDatabaseCount('attachments', $initialAttachmentCount + count($attachments));
    $this->assertEquals(
        $initialQuoteAttachmentCount + count($attachments),
        \App\Models\Attachment::where('quote_id', $quote->id)->count()
    );

    $this->assertDatabaseHas('attachments', [
        'quote_id' => $quote->id,
        'user_id' => $specialist->id,
        'position' => 1,
        'mime_type' => 'image/jpeg',
    ]);

    $this->assertDatabaseHas('attachments', [
        'quote_id' => $quote->id,
        'user_id' => $specialist->id,
        'position' => 2,
        'mime_type' => 'image/jpeg',
    ]);

    $this->assertDatabaseHas('attachments', [
        'quote_id' => $quote->id,
        'user_id' => $specialist->id,
        'position' => 3,
        'mime_type' => 'video/mp4',
    ]);

    $positions = \App\Models\Attachment::where('quote_id', $quote->id)
        ->orderBy('position')
        ->pluck('position')
        ->toArray();

    $expectedPositions = range(1, count($attachments) + $initialQuoteAttachmentCount);
    $this->assertEquals($expectedPositions, $positions);
});
it('can update Quote attachments by removing', function () {
    $this->seed();
    $customer = \App\Models\User::factory()->customer()->verified()->create();
    $specialist = \App\Models\User::factory()->specialist()->verified()->create();
    $listing = \App\Models\Listing::factory()->create(['user_id' => $customer->id]);
    $quote = \App\Models\Quote::factory()->create(['listing_id' => $listing->id, 'user_id' => $specialist->id]);
    $this->actingAs($specialist);

    $initialAttachmentCount = \App\Models\Attachment::count();

    $attachments = [
        UploadedFile::fake()->image('test1.jpg'),
        UploadedFile::fake()->image('test2.jpg'),
        UploadedFile::fake()->create('test3.mp4', 1024, 'video/mp4'),
    ];

    $this->post(route('quotes.addAttachments', ['quote' => $quote]), [
        'attachments' => $attachments,
    ]);

    $addedAttachments = \App\Models\Attachment::where('quote_id', $quote->id)->get();
    $this->assertCount(count($attachments), $addedAttachments, 'Attachments were not created properly');

    $attachmentCount = $addedAttachments->count();
    $attachmentIds = $addedAttachments->pluck('id')->toArray();

    $response = $this->put(route('quotes.updateAttachments', ['quote' => $quote]), [
        'delete_attachments' => $attachmentIds,
    ]);

    $response->assertStatus(302);
    $response->assertSessionHas('success', 'Quote attachments updated successfully.');

    $this->assertEquals(
        0,
        \App\Models\Attachment::where('quote_id', $quote->id)->count(),
        'Attachments were not deleted properly'
    );

    foreach ($attachmentIds as $id) {
        $this->assertDatabaseMissing('attachments', [
            'id' => $id,
            'deleted_at' => null
        ]);
    }

    // Verify each attachment is soft-deleted
    foreach ($attachmentIds as $id) {
        $this->assertSoftDeleted(\App\Models\Attachment::withTrashed()->find($id));
    }

    // Verify the specific timestamps
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
it('can update Quote attachments by reordering', function () {
    $this->seed();
    DB::beginTransaction();

    try {
        $customer = \App\Models\User::factory()->customer()->verified()->create();
        $specialist = \App\Models\User::factory()->specialist()->verified()->create();
        $listing = \App\Models\Listing::factory()->create(['user_id' => $customer->id]);
        $quote = \App\Models\Quote::factory()->create(['listing_id' => $listing->id, 'user_id' => $specialist->id]);
        $this->actingAs($specialist);
        $countAttachments = \App\Models\Attachment::count();
        $this->assertNotNull($quote->id, "Quote wasn't created properly");

        $attachments = [
            UploadedFile::fake()->image('test1.jpg')->size(100),
            UploadedFile::fake()->image('test2.jpg')->size(100),
        ];

        $response = $this->post(route('quotes.updateAttachments', ['quote' => $quote]), [
            'attachments' => $attachments,
        ]);

        dump("Response status: " . $response->getStatusCode());
        dump("Response content: " . $response->getContent());

        $createdAttachments = \App\Models\Attachment::where('quote_id', $quote->id)->get();
        dump("Created attachments count: " . $createdAttachments->count());

        if ($createdAttachments->isEmpty()) {
            for ($i = 1; $i <= 3; $i++) {
                \App\Models\Attachment::create([
                    'user_id' => $specialist->id,
                    'quote_id' => $quote->id,
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

        $response = $this->put(route('quotes.updateAttachments', ['quote' => $quote]), [
            'positions' => $positions,
        ]);

        $response->assertStatus(302);
        $response->assertSessionHas('success', 'Quote attachments updated successfully.');

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


/*** MY QUOTES */
it('redirects to the login page when accessing the My Quotes page as a guest user', function () {
    /*
     * @var \Illuminate\Foundation\Testing\TestResponse $response
     */
    $response = $this->get(route('quotes.index'));
    $response->assertRedirecttoRoute('login');
    $response->assertStatus(302);
});
it('can access the My Quotes page as a logged-in, verified user with the customer role and no quotes', function () {
    $user = \App\Models\User::factory()->customer()->verified()->create();
    $this->actingAs($user);
    /*
     * @var \Illuminate\Foundation\Testing\TestResponse $response
     */
    $response = $this->get(route('quotes.index'));
    $response->assertOK();
    $response->assertSeeInOrder([
        'Quotes',
        'Quotes Received',
        'No quotes received',
        'You haven',
        't received any quotes yet.',
    ]);
});
it('can access the My Quotes page as a logged-in, verified user with the specialist role and no quotes', function () {
    $user = \App\Models\User::factory()->specialist()->verified()->create();
    $this->actingAs($user);
    /*
     * @var \Illuminate\Foundation\Testing\TestResponse $response
     */
    $response = $this->get(route('quotes.index'));
    $response->assertOK();
    $response->assertSeeInOrder([
        'Quotes',
        'Quotes Submitted',
        'No quotes submitted',
        'You haven',
        't submitted any quotes yet.',
    ]);
});
it('can access the My Quotes page, including open quotes, as a logged-in, verified user with the customer role', function () {
    $customer = \App\Models\User::factory()->customer()->verified()->create();
    $listing = \App\Models\Listing::factory()->create(['user_id' => $customer->id]);
    $specialist = \App\Models\User::factory()->specialist()->verified()->create();
    $quote = \App\Models\Quote::factory()->create(['user_id' => $specialist->id, 'listing_id' => $listing->id]);
    $this->actingAs($customer);
    /*
     * @var \Illuminate\Foundation\Testing\TestResponse $response
     */
    $response = $this->get(route('quotes.index'));
    $response->assertOK();
    $response->assertSeeInOrder([
        'Quotes',
        'Quotes Received',
        '<th>Image</th>',
        '<th>Listing</th>',
        '<th>Expiry</th>',
        '<th>Status</th>',
        '<th>Delivery Method</th>',
        '<th',
        '>Amount</th>',
        '<th>Updated</th>',
        '<th',
        '>Actions</th>',
        '<a href="' . route('listings.show', ['listing' => $listing]) . '"',
        $listing->title,
        '</a>',
        $listing->getExpiryDateAttribute()->format('Y-m-d'),
        $quote->status->name,
        $quote->deliveryMethod->name,
        $quote->currency->iso_code . ' ' . number_format($quote->amount, 2),
        $quote->getUpdatedDate(),
        '<a href="' . route('quotes.show', ['quote' => $quote]) . '"',
        'View',
    ]);
});
it('can access the My Quotes page, including open quotes, as a logged-in, verified user with the specialist role', function () {
    $customer = \App\Models\User::factory()->customer()->verified()->create();
    $listing = \App\Models\Listing::factory()->create(['user_id' => $customer->id]);
    $specialist = \App\Models\User::factory()->specialist()->verified()->create();
    $quote = \App\Models\Quote::factory()->create(['user_id' => $specialist->id, 'listing_id' => $listing->id]);
    $this->actingAs($specialist);
    /*
     * @var \Illuminate\Foundation\Testing\TestResponse $response
     */
    $response = $this->get(route('quotes.index'));
    $response->assertOK();
    $response->assertSeeInOrder([
        'Quotes',
        'Quotes Submitted',
        '<th>Image</th>',
        '<th>Listing</th>',
        '<th>Expiry</th>',
        '<th>Status</th>',
        '<th>Delivery Method</th>',
        '<th',
        '>Amount</th>',
        '<th>Updated</th>',
        '<th',
        '>Actions</th>',
        '<a href="' . route('listings.show', ['listing' => $listing]) . '"',
        $listing->title,
        '</a>',
        $listing->getExpiryDateAttribute()->format('Y-m-d'),
        $quote->status->name,
        $quote->deliveryMethod->name,
        $quote->currency->iso_code . ' ' . number_format($quote->amount, 2),
        $quote->getUpdatedDate(),
        '<a href="' . route('quotes.show', ['quote' => $quote]) . '"',
        'view',
        '<a href="' . route('quotes.edit', ['quote' => $quote]) . '"',
        'edit',
        '<button',
        '>',
        'delete',
        '</button>',
    ]);
});


/** CREATE QUOTE */
it('does not allow a Quote to be created with empty data', function () {
    $this->seed();
    $customer = \App\Models\User::factory()->customer()->verified()->create();
    $specialist = \App\Models\User::factory()->specialist()->verified()->create();
    $listing = \App\Models\Listing::factory()->create(['user_id' => $customer->id]);
    $this->actingAs($specialist);
    /*
     * @var \Illuminate\Foundation\Testing\TestResponse $response
     */
    $response = $this->post(route('quotes.store'), [
        'listing_id' => $listing->id,
        'currency_id' => '',
        'deliverymethod_id' => '',
        'amount' => '',
        'turnaround' => '',
        'description' => '',
        'use_default_location' => '',
        'address_line1' => '',
        'address_line2' => '',
        'city' => '',
        'postcode' => '',
        'country_id' => '',
        'phone' => '',
    ]);
    $response->assertStatus(302);
    $response->assertSessionHasErrors([
        'currency_id',
        'deliverymethod_id',
        'amount',
        'turnaround',
        'use_default_location',
        'address_line1',
        'city',
        'country_id',
        'postcode',
    ]);
});
it('does not allow a Quote to be created with invalid data', function () {
    $this->seed();
    $customer = \App\Models\User::factory()->customer()->verified()->create();
    $specialist = \App\Models\User::factory()->specialist()->verified()->create();
    $listing = \App\Models\Listing::factory()->create(['user_id' => $customer->id]);
    $this->actingAs($specialist);
    /*
     * @var \Illuminate\Foundation\Testing\TestResponse $response
     */
    $response = $this->post(route('quotes.store'), [
        'listing_id' => $listing->id,
        'currency_id' => 999,
        'deliverymethod_id' => 999,
        'amount' => -500,
        'turnaround' => -20,
        'description' => 'Description',
        'use_default_location' => true,
        'address_line1' => '123 Main St',
        'address_line2' => null,
        'city' => 'Test City',
        'postcode' => 'FAKE 123',
        'country_id' => 999,
        'phone' => 'ABCABC',
    ]);
    $response->assertStatus(302);
    $response->assertSessionHasErrors([
        'currency_id',
        'deliverymethod_id',
        'amount',
        'turnaround',
        'country_id',
        'phone',
    ]);
});
it('allows a Quote to be created with valid data', function () {
    $this->seed();
    $customer = \App\Models\User::factory()->customer()->verified()->create();
    $specialist = \App\Models\User::factory()->specialist()->verified()->create();
    $listing = \App\Models\Listing::factory()->create(['user_id' => $customer->id]);
    $this->actingAs($specialist);
    /*
     * @var \Illuminate\Foundation\Testing\TestResponse $response
     */
    $response = $this->post(route('quotes.store'), [
        'user_id' => $specialist->id,
        'status_id' => 1,
        'listing_id' => $listing->id,
        'currency_id' => 1,
        'deliverymethod_id' => 1,
        'amount' => 500,
        'turnaround' => 20,
        'description' => 'Description',
        'use_default_location' => true,
        'address_line1' => '123 Main St',
        'address_line2' => null,
        'city' => 'Test City',
        'postcode' => 'FAKE 123',
        'country_id' => 1,
        'phone' => '+1234567890',
    ]);
    $response->assertSessionHas('success', 'Quote created successfully! The customer has been notified.');
    $newQuote = \App\Models\Quote::latest('id')->first();
    $response->assertStatus(302);
    $response->assertRedirect(route('quotes.show', ['quote' => $newQuote->id]));
});

/** UPDATE QUOTE */
it('cannot update the Quote with empty data', function () {
    $this->seed();
    $customer = \App\Models\User::factory()->customer()->verified()->create();
    $specialist = \App\Models\User::factory()->specialist()->verified()->create();
    $listing = \App\Models\Listing::factory()->create(['user_id' => $customer->id]);
    $quote = \App\Models\Quote::factory()->create(['listing_id' => $listing->id, 'user_id' => $specialist->id]);
    $this->actingAs($specialist);
    /*
     * @var \Illuminate\Foundation\Testing\TestResponse $response
     */
    $response = $this->put(route('quotes.update', ['quote' => $quote]), [
        'currency_id' => '',
        'deliverymethod_id' => '',
        'amount' => '',
        'turnaround' => '',
        'description' => '',
        'use_default_location' => '',
        'address_line1' => '',
        'address_line2' => '',
        'city' => '',
        'postcode' => '',
        'country_id' => '',
        'phone' => '',
    ]);
    $response->assertStatus(302);
    $response->assertSessionHasErrors([
        'currency_id',
        'deliverymethod_id',
        'amount',
        'turnaround',
        'use_default_location',
        'address_line1',
        'city',
        'country_id',
        'postcode',
    ]);
});
it('cannot update the Quote with invalid data', function () {
    $this->seed();
    $customer = \App\Models\User::factory()->customer()->verified()->create();
    $specialist = \App\Models\User::factory()->specialist()->verified()->create();
    $listing = \App\Models\Listing::factory()->create(['user_id' => $customer->id]);
    $quote = \App\Models\Quote::factory()->create(['listing_id' => $listing->id, 'user_id' => $specialist->id]);
    $this->actingAs($specialist);
    /*
     * @var \Illuminate\Foundation\Testing\TestResponse $response
     */
    $response = $this->put(route('quotes.update', ['quote' => $quote]), [
        'listing_id' => $listing->id,
        'user_id' => $specialist->id,
        'status_id' => 999,
        'currency_id' => 999,
        'deliverymethod_id' => 999,
        'amount' => -500,
        'turnaround' => -20,
        'description' => 'Description',
        'use_default_location' => true,
        'address_line1' => '123 Main St',
        'address_line2' => null,
        'city' => 'Test City',
        'postcode' => 'FAKE 123',
        'country_id' => 999,
        'phone' => 'ABCABC',
    ]);
    $response->assertStatus(302);
    $response->assertSessionHasErrors([
        'status_id',
        'currency_id',
        'deliverymethod_id',
        'amount',
        'turnaround',
        'country_id',
        'phone',
    ]);
});
it('can update the Quote with valid data', function () {
    $this->seed();
    $customer = \App\Models\User::factory()->customer()->verified()->create();
    $specialist = \App\Models\User::factory()->specialist()->verified()->create();
    $listing = \App\Models\Listing::factory()->create(['user_id' => $customer->id]);
    $quote = \App\Models\Quote::factory()->create(['listing_id' => $listing->id, 'user_id' => $specialist->id]);
    $this->actingAs($specialist);
    /*
     * @var \Illuminate\Foundation\Testing\TestResponse $response
     */
    $response = $this->put(route('quotes.update', ['quote' => $quote]), [
        'listing_id' => $listing->id,
        'user_id' => $specialist->id,
        'status_id' => 1,
        'currency_id' => 1,
        'deliverymethod_id' => 1,
        'amount' => 500,
        'turnaround' => 20,
        'description' => 'Description',
        'use_default_location' => true,
        'address_line1' => '123 Main St',
        'address_line2' => null,
        'city' => 'Test City',
        'postcode' => 'FAKE 123',
        'country_id' => 1,
        'phone' => '+1234567890',
    ]);
    $response->assertSessionHas('success', 'Quote updated successfully.');
    $response->assertStatus(302);
    $response->assertRedirect(route('quotes.show', ['quote' => $quote->id]));
});

/** DELETE QUOTE */
it('can delete a Quote as the quote\'s owner', function () {
    $this->seed();
    $customer = \App\Models\User::factory()->customer()->verified()->create();
    $specialist = \App\Models\User::factory()->specialist()->verified()->create();
    $listing = \App\Models\Listing::factory()->create(['user_id' => $customer->id]);
    $quote = \App\Models\Quote::factory()->create(['listing_id' => $listing->id, 'user_id' => $specialist->id]);
    $this->actingAs($specialist);
    /*
     * @var \Illuminate\Foundation\Testing\TestResponse $response
     */
    $response = $this->delete(route('quotes.destroy', ['quote' => $quote]));
    $response->assertSessionHas('success', 'Quote retracted successfully.');
    $response->assertStatus(302);
    $response->assertRedirect(route('quotes.index'));
});
it('cannot delete a Quote created by another user', function () {
    $this->seed();
    $customer = \App\Models\User::factory()->customer()->verified()->create();
    $specialist = \App\Models\User::factory()->specialist()->verified()->create();
    $listing = \App\Models\Listing::factory()->create(['user_id' => $customer->id]);
    $quote = \App\Models\Quote::factory()->create(['listing_id' => $listing->id, 'user_id' => $specialist->id]);
    $this->actingAs($customer);
    /*
     * @var \Illuminate\Foundation\Testing\TestResponse $response
     */
    $response = $this->delete(route('quotes.destroy', ['quote' => $quote]));
    $response->assertStatus(302);
    $response->assertRedirect(route('quotes.show', ['quote' => $quote]));
    $response->assertSessionHas('error', 'You do not have permission to delete this quote.');
});
it('cannot delete a Quote that has a status not equal to "Open"', function () {
    $this->seed();
    $customer = \App\Models\User::factory()->customer()->verified()->create();
    $specialist = \App\Models\User::factory()->specialist()->verified()->create();
    $listing = \App\Models\Listing::factory()->create(['user_id' => $customer->id]);
    $quote = \App\Models\Quote::factory()->create(['listing_id' => $listing->id, 'user_id' => $specialist->id]);
    $quote->status_id = 2; // Assuming 2 is not "Open"
    $quote->save();
    $this->actingAs($specialist);
    /*
     * @var \Illuminate\Foundation\Testing\TestResponse $response
     */
    $response = $this->delete(route('quotes.destroy', ['quote' => $quote]));
    $response->assertStatus(302);
    $response->assertRedirect(route('quotes.show', ['quote' => $quote]));
    $response->assertSessionHas('error', 'This quote cannot be deleted because its status is not "Open".');
});