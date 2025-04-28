<?php

it('is redirected to the login page when accessing the listing create page as a guest user', function () {
    /*
     * @var \Illuminate\Foundation\Testing\TestResponse $response
     */
    $response = $this->get(route('listings.create'));
    $response->assertRedirecttoRoute('login');
    $response->assertStatus(302);
});

it('can access the listing create page as a logged-in, verified user with the customer role', function () {
    $user = \App\Models\User::factory()->customer()->verified()->create();
    $this->actingAs($user);
    /*
     * @var \Illuminate\Foundation\Testing\TestResponse $response
     */
    $response = $this->get(route('listings.create'));
    $response->assertOK();
});