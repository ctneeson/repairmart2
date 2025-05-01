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
});
it('cannot access the Create Order page as a logged-in user with the customer role and a different id than the listing for which the order is being created', function () {
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
});


/*** SHOW ORDER */
it('redirects to the login page when accessing the View Order page as a guest user', function () {
    $customer = \App\Models\User::factory()->customer()->verified()->create();
    $listing = \App\Models\Listing::factory()->create(['user_id' => $customer->id]);
    $specialist = \App\Models\User::factory()->specialist()->verified()->create();
    $quote = \App\Models\Quote::factory()->create(['listing_id' => $listing->id, 'user_id' => $specialist->id]);
    $order = \App\Models\Order::factory()->create(['quote_id' => $quote->id]);
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
    $order = \App\Models\Order::factory()->create(['quote_id' => $quote->id]);
    /*
     * @var \Illuminate\Foundation\Testing\TestResponse $response
     */
    $this->actingAs($customer);
    $response = $this->get(route('orders.show', ['order' => $order]));
    $response->assertOK();
});
it('can access the View Order page as the order\'s specialist', function () {
    $customer = \App\Models\User::factory()->customer()->verified()->create();
    $specialist = \App\Models\User::factory()->specialist()->verified()->create();
    $listing = \App\Models\Listing::factory()->create(['user_id' => $customer->id]);
    $quote = \App\Models\Quote::factory()->create(['listing_id' => $listing->id, 'user_id' => $specialist->id]);
    $order = \App\Models\Order::factory()->create(['quote_id' => $quote->id]);
    /*
     * @var \Illuminate\Foundation\Testing\TestResponse $response
     */
    $this->actingAs($specialist);
    $response = $this->get(route('orders.show', ['order' => $order]));
    $response->assertOK();
});