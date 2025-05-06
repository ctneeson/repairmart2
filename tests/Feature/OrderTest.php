<?php

/*** CREATE ORDER */
it('redirects to the login page when accessing the Create Order page as a guest user', function () {
    $customer = \App\Models\User::factory()->customer()->verified()->create();
    $specialist = \App\Models\User::factory()->specialist()->verified()->create();
    $listing = \App\Models\Listing::factory()->create(['user_id' => $customer->id]);
    $quote = \App\Models\Quote::factory()->create(['listing_id' => $listing->id, 'user_id' => $specialist->id]);
    /*
     * @var \Illuminate\Foundation\Testing\TestResponse $response
     */
    $response = $this->get(route('orders.create', ['quote' => $quote]));
    $response->assertRedirecttoRoute('login');
    $response->assertStatus(302);
});
it('cannot access the Create Order page as a logged-in user with the specialist role', function () {
    $customer = \App\Models\User::factory()->customer()->verified()->create();
    $listing = \App\Models\Listing::factory()->create(['user_id' => $customer->id]);
    $specialist = \App\Models\User::factory()->specialist()->verified()->create();
    $quote = \App\Models\Quote::factory()->create(['listing_id' => $listing->id, 'user_id' => $specialist->id]);
    /*
     * @var \Illuminate\Foundation\Testing\TestResponse $response
     */
    $this->actingAs($specialist);
    $response = $this->get(route('orders.create', ['quote' => $quote]));
    $response->assertRedirecttoRoute('quotes.show', ['quote' => $quote]);
    $response->assertStatus(302);
    $response->assertSessionHas('error', 'Only the customer or an admin can create an order from this quote.');
});
it('cannot access the Create Order page as a logged-in user with the customer role for another user\'s listing', function () {
    $customer = \App\Models\User::factory()->customer()->verified()->create();
    $listing = \App\Models\Listing::factory()->create(['user_id' => $customer->id]);
    $specialist = \App\Models\User::factory()->specialist()->verified()->create();
    $quote = \App\Models\Quote::factory()->create(['listing_id' => $listing->id, 'user_id' => $specialist->id]);
    $customer2 = \App\Models\User::factory()->customer()->verified()->create();
    /*
     * @var \Illuminate\Foundation\Testing\TestResponse $response
     */
    $this->actingAs($customer2);
    $response = $this->get(route('orders.create', ['quote' => $quote]));
    $response->assertRedirecttoRoute('quotes.show', ['quote' => $quote]);
    $response->assertStatus(302);
    $response->assertSessionHas('error', 'Only the customer or an admin can create an order from this quote.');
});
it('can access the Create Order page as a logged-in user with the customer role', function () {
    $customer = \App\Models\User::factory()->customer()->verified()->create();
    $listing = \App\Models\Listing::factory()->create(['user_id' => $customer->id]);
    $specialist = \App\Models\User::factory()->specialist()->verified()->create();
    $quote = \App\Models\Quote::factory()->create(['listing_id' => $listing->id, 'user_id' => $specialist->id]);
    /*
     * @var \Illuminate\Foundation\Testing\TestResponse $response
     */
    $this->actingAs($customer);
    $response = $this->get(route('orders.create', ['quote' => $quote]));
    $response->assertOK();
    $response->assertSeeInOrder([
        'Create Order',
        'You are about to create an order based on the quote from ',
        $quote->repairSpecialist->name,
        ' for your repair request',
        $quote->listing->title,
        'Quote Details',
        'Amount',
        $quote->currency->iso_code . ' ' . number_format($quote->amount, 2),
        'Estimated Turnaround',
        $quote->turnaround . ' days',
        'Delivery Method',
        $quote->deliveryMethod->name,
        'Quote Description',
        $quote->description,
        'Listing Details',
        $listing->title,
        '<a href="' . route('listings.show', ['listing' => $listing]) . '"',
        'view listing',
        '</a>',
        'Manufacturer',
        $listing->manufacturer->name,
        'Product(s)',
        'Specialist',
        '<a href="' . route('profile.show', ['user' => $specialist]),
        $specialist->name,
        '</a>',
        $quote->address_line1,
        !empty($quote->address_line2) ? $quote->address_line2 : '',
        $quote->city . ', ' . $quote->country->name,
        $quote->postcode,
        !empty($quote->phone) ? $quote->phone : '',
        'Customer',
        '<a href="' . route('profile.show', ['user' => $customer]),
        $customer->name,
        '</a>',
        $listing->address_line1,
        !empty($listing->address_line2) ? $listing->address_line2 : '',
        $listing->city . ', ' . $listing->country->name,
        $listing->postcode,
        !empty($listing->phone) ? $listing->phone : '',
        'Add comment (optional)',
        '<textarea name="comment"',
        '</textarea>',
        '<button type="submit"',
        'Create Order',
        '</button>',
    ]);
    foreach ($listing->products as $product) {
        $response->assertSee($product->category . ' > ' . $product->subcategory);
    }
});


/*** SHOW ORDER */
it('redirects to the login page when accessing the View Order page as a guest user', function () {
    $customer = \App\Models\User::factory()->customer()->verified()->create();
    $specialist = \App\Models\User::factory()->specialist()->verified()->create();
    $listing = \App\Models\Listing::factory()->create(['user_id' => $customer->id]);
    $quote = \App\Models\Quote::factory()->create(['listing_id' => $listing->id, 'user_id' => $specialist->id]);
    $order = \App\Models\Order::factory()->forQuote($quote)->create();
    /*
     * @var \Illuminate\Foundation\Testing\TestResponse $response
     */
    $response = $this->get(route('orders.show', ['order' => $order]));
    $response->assertRedirecttoRoute('login');
    $response->assertStatus(302);
});
it('can access the View Order page as the order\'s customer', function () {
    $customer = \App\Models\User::factory()->customer()->verified()->create();
    $specialist = \App\Models\User::factory()->specialist()->verified()->create();
    $listing = \App\Models\Listing::factory()->create(['user_id' => $customer->id]);
    $quote = \App\Models\Quote::factory()->create(['listing_id' => $listing->id, 'user_id' => $specialist->id]);
    $order = \App\Models\Order::factory()->forQuote($quote)->create();
    /*
     * @var \Illuminate\Foundation\Testing\TestResponse $response
     */
    $this->actingAs($customer);
    $response = $this->get(route('orders.show', ['order' => $order]));
    $response->assertOK();
    $response->assertSeeInOrder([
        'Order #' . $order->id,
        $order->status->name,
        'Order Details',
        'Amount',
        $order->currency->iso_code . ' ' . number_format($order->amount, 2),
        'Estimated Turnaround',
        $order->turnaround . ' days',
        'Delivery Method',
        $order->deliveryMethod->name,
        'Quote Description',
        $quote->description,
        !($order->quote->attachments->count() > 0) ? '' : 'Quote Attachments',
        'Listing Details',
        $listing->title,
        '<a href="' . route('listings.show', ['listing' => $listing]) . '"',
        'view',
        '</a>',
        'Manufacturer',
        $listing->manufacturer->name,
        'Product(s)',
        'Specialist',
        '<a href="' . route('profile.show', ['user' => $specialist]),
        $specialist->name,
        '</a>',
        $quote->address_line1,
        !empty($quote->address_line2) ? $quote->address_line2 : '',
        $quote->city . ', ' . $quote->country->name,
        $quote->postcode,
        !empty($quote->phone) ? $quote->phone : '',
        'Customer',
        '<a href="' . route('profile.show', ['user' => $customer]),
        $customer->name,
        '</a>',
        $listing->address_line1,
        !empty($listing->address_line2) ? $listing->address_line2 : '',
        $listing->city . ', ' . $listing->country->name,
        $listing->postcode,
        !empty($listing->phone) ? $listing->phone : '',
        !($order->comments->count() > 0) ? '' : 'Comment History',
    ]);
    foreach ($listing->products as $product) {
        $response->assertSee($product->category . ' > ' . $product->subcategory);
    }
    foreach ($order->comments as $comment) {
        $response->assertSeeInOrder([
            $comment->user->name,
            $comment->comment,
            $comment->created_at->format('Y-m-d, H:i')
        ]);
    }
});
it('can access the View Order page as the order\'s specialist', function () {
    $customer = \App\Models\User::factory()->customer()->verified()->create();
    $specialist = \App\Models\User::factory()->specialist()->verified()->create();
    $listing = \App\Models\Listing::factory()->create(['user_id' => $customer->id]);
    $quote = \App\Models\Quote::factory()->create(['listing_id' => $listing->id, 'user_id' => $specialist->id]);
    $order = \App\Models\Order::factory()->forQuote($quote)->create();
    /*
     * @var \Illuminate\Foundation\Testing\TestResponse $response
     */
    $this->actingAs($specialist);
    $response = $this->get(route('orders.show', ['order' => $order]));
    $response->assertOK();
    $response->assertSeeInOrder([
        'Order #' . $order->id,
        $order->status->name,
        'Order Details',
        'Amount',
        $order->currency->iso_code . ' ' . number_format($order->amount, 2),
        'Estimated Turnaround',
        $order->turnaround . ' days',
        'Delivery Method',
        $order->deliveryMethod->name,
        'Quote Description',
        $quote->description,
        !($order->quote->attachments->count() > 0) ? '' : 'Quote Attachments',
        'Listing Details',
        $listing->title,
        '<a href="' . route('listings.show', ['listing' => $listing]) . '"',
        'view',
        '</a>',
        'Manufacturer',
        $listing->manufacturer->name,
        'Product(s)',
        'Specialist',
        '<a href="' . route('profile.show', ['user' => $specialist]),
        $specialist->name,
        '</a>',
        $quote->address_line1,
        !empty($quote->address_line2) ? $quote->address_line2 : '',
        $quote->city . ', ' . $quote->country->name,
        $quote->postcode,
        !empty($quote->phone) ? $quote->phone : '',
        'Customer',
        '<a href="' . route('profile.show', ['user' => $customer]),
        $customer->name,
        '</a>',
        $listing->address_line1,
        !empty($listing->address_line2) ? $listing->address_line2 : '',
        $listing->city . ', ' . $listing->country->name,
        $listing->postcode,
        !empty($listing->phone) ? $listing->phone : '',
        !($order->comments->count() > 0) ? '' : 'Comment History',
    ]);
    foreach ($listing->products as $product) {
        $response->assertSee($product->category . ' > ' . $product->subcategory);
    }
    foreach ($order->comments as $comment) {
        $response->assertSeeInOrder([
            $comment->user->name,
            $comment->comment,
            $comment->created_at->format('Y-m-d, H:i')
        ]);
    }
});