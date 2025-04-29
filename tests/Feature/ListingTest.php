<?php

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

/*** SHOW LISTING */
it('can access the Show Listing page as a logged-in, verified user', function () {
    $user = \App\Models\User::factory()->verified()->create();
    $secondUser = \App\Models\User::factory()->create();
    $this->actingAs($user);
    $listing = \App\Models\Listing::factory()->create(['user_id' => $secondUser->id]);
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