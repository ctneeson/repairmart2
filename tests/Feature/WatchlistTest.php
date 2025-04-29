<?php

it('redirects to the login page when accessing the Watchlist page as a guest user', function () {
    /*
     * @var \Illuminate\Foundation\Testing\TestResponse $response
     */
    $response = $this->get(route('watchlist.index'));
    $response->assertRedirecttoRoute('login');
    $response->assertStatus(302);
});
it('can access the Watchlist page as a logged-in, verified user', function () {
    $user = \App\Models\User::factory()->verified()->create();
    $this->actingAs($user);
    /*
     * @var \Illuminate\Foundation\Testing\TestResponse $response
     */
    $response = $this->get(route('watchlist.index'));
    $response->assertOK();
    $response->assertSee('Listings - Watchlist');
});