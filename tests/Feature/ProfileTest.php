<?php

/*** USER PROFILE MANAGEMENT */
it('redirects to the login page when accessing user profile management page as guest user', function () {
    /*
     * @var \Illuminate\Foundation\Testing\TestResponse $response
     */
    $response = $this->get(route('profile.index'));
    $response->assertRedirecttoRoute('login');
    $response->assertStatus(302);
});
it('can access the User Profile Management page as a logged-in user', function () {
    $user = \App\Models\User::factory()->create();
    $this->actingAs($user);
    /*
     * @var \Illuminate\Foundation\Testing\TestResponse $response
     */
    $response = $this->get(route('profile.index'));
    $response->assertOK();
});

/*** USER PROFILE DISPLAY */
it('redirects to the login page when accessing user profile page as guest user', function () {
    /*
     * @var \Illuminate\Foundation\Testing\TestResponse $response
     */
    $response = $this->get(route('profile.show', ['user' => 1]));
    $response->assertRedirecttoRoute('login');
    $response->assertStatus(302);
});
it('can access the User Profile Display page as a logged-in user', function () {
    $user = \App\Models\User::factory()->create();
    $this->actingAs($user);
    /*
     * @var \Illuminate\Foundation\Testing\TestResponse $response
     */
    $response = $this->get(route('profile.show', ['user' => $user->id]));
    $response->assertOK();
});
it('can access the User Profile Display page as a logged-in user with a different ID', function () {
    $user = \App\Models\User::factory()->create();
    $secondUser = \App\Models\User::factory()->create();
    $this->actingAs($user);
    /*
     * @var \Illuminate\Foundation\Testing\TestResponse $response
     */
    $response = $this->get(route('profile.show', ['user' => $secondUser->id]));
    $response->assertOK();
});

/*** USER PROFILE SEARCH */
it('redirects to the login page when accessing user profile search page as guest user', function () {
    /*
     * @var \Illuminate\Foundation\Testing\TestResponse $response
     */
    $response = $this->get(route('profile.search'));
    $response->assertRedirecttoRoute('login');
    $response->assertStatus(302);
});
it('can access the User Profile Search page as a logged-in admin user', function () {
    $user = \App\Models\User::factory()->admin()->create();
    $this->actingAs($user);
    /*
     * @var \Illuminate\Foundation\Testing\TestResponse $response
     */
    $response = $this->get(route('profile.search'));
    $response->assertOK();
});
it('cannot access the User Profile Search page as a logged-in non-admin user', function () {
    $user = \App\Models\User::factory()->create();
    $this->actingAs($user);
    /*
     * @var \Illuminate\Foundation\Testing\TestResponse $response
     */
    $response = $this->get(route('profile.search'));
    $response->assertForbidden();
});