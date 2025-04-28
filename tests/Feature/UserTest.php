<?php

it('redirects to the login page when accessing user profile management page as guest user', function () {
    /*
     * @var \Illuminate\Foundation\Testing\TestResponse $response
     */
    $response = $this->get(route('profile.index'));
    $response->assertRedirecttoRoute('login');
    $response->assertStatus(302);
});

it('redirects to the login page when accessing user profile page as guest user', function () {
    /*
     * @var \Illuminate\Foundation\Testing\TestResponse $response
     */
    $response = $this->get(route('profile.show', ['user' => 1]));
    $response->assertRedirecttoRoute('login');
    $response->assertStatus(302);
});

it('redirects to the login page when accessing user profile search page as guest user', function () {
    /*
     * @var \Illuminate\Foundation\Testing\TestResponse $response
     */
    $response = $this->get(route('profile.search'));
    $response->assertRedirecttoRoute('login');
    $response->assertStatus(302);
});