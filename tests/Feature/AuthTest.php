<?php

it('returns success on login page', function () {
    $response = $this->get(route('login'));

    $response->assertStatus(200);
});

it('returns success on signup page', function () {
    $response = $this->get(route('signup'));

    $response->assertStatus(200);
});

it('returns success on forgot password page', function () {
    $response = $this->get(route('password.reset-request'));

    $response->assertStatus(200);
});
