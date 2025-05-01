<?php

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

/*** VIEW QUOTE */
it('redirects to the login page when accessing the View Quote page as a guest user', function () {
    /*
     * @var \Illuminate\Foundation\Testing\TestResponse $response
     */
    $response = $this->get(route('quotes.show', ['quote' => 1]));
    $response->assertRedirecttoRoute('login');
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
});
it('can access the View Quote page as the user who created the listing for which the quote was submitted', function () {
    $user = \App\Models\User::factory()->customer()->verified()->create();
    $this->actingAs($user);
    $listing = \App\Models\Listing::factory()->create(['user_id' => $user->id]);
    $quote = \App\Models\Quote::factory()->create(['listing_id' => $listing->id]);
    /*
     * @var \Illuminate\Foundation\Testing\TestResponse $response
     */
    $response = $this->get(route('quotes.show', ['quote' => $quote]));
    $response->assertOK();
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
});
it('cannot access the Edit Quote page as the user who created the listing for which the quote was submitted', function () {
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

/*** EDIT QUOTE */
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

/*** MY QUOTES */
it('redirects to the login page when accessing the My Quotes page as a guest user', function () {
    /*
     * @var \Illuminate\Foundation\Testing\TestResponse $response
     */
    $response = $this->get(route('quotes.index'));
    $response->assertRedirecttoRoute('login');
    $response->assertStatus(302);
});
it('can access the My Quotes page as a logged-in, verified user with the customer role', function () {
    $user = \App\Models\User::factory()->customer()->verified()->create();
    $this->actingAs($user);
    /*
     * @var \Illuminate\Foundation\Testing\TestResponse $response
     */
    $response = $this->get(route('quotes.index'));
    $response->assertOK();
});
it('can access the My Quotes page as a logged-in, verified user with the specialist role', function () {
    $user = \App\Models\User::factory()->specialist()->verified()->create();
    $this->actingAs($user);
    /*
     * @var \Illuminate\Foundation\Testing\TestResponse $response
     */
    $response = $this->get(route('quotes.index'));
    $response->assertOK();
});
