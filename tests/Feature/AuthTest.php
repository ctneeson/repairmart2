<?php

/** LOGIN */
it('returns success on Login page', function () {
    $response = $this->get(route('login'));

    $response->assertStatus(200);
    $response->assertSee('Login');
    $response->assertSee('Forgot password?');
    $response->assertSee('<a href="' . route('password.reset-request') . '"', false);
    $response->assertSee('Click here to create one');
    $response->assertSee('<a href="' . route('signup') . '"', false);
    $response->assertSee('Google');
    $response->assertSee('<a href="' . route('login.oauth', 'google') . '"', false);
    $response->assertSee('Facebook');
    $response->assertSee('<a href="' . route('login.oauth', 'facebook') . '"', false);
});
it('does not permit login with incorrect credentials', function () {
    \App\Models\User::factory()->create([
        'email' => 'user@user.com',
        'password' => bcrypt('password'),
    ]);
    /*
     * @var \Illuminate\Foundation\Testing\TestResponse $response
     */
    $response = $this->post(route('login.store'), [
        'email' => 'user@user.com',
        'password' => 'wrongpassword',
    ]);
    $response->assertSessionHasErrors(['email']);
    $response->assertStatus(302);
});
it('permits login with correct credentials', function () {
    \App\Models\User::factory()->create([
        'email' => 'user@user.com',
        'password' => bcrypt('password'),
    ]);
    /*
     * @var \Illuminate\Foundation\Testing\TestResponse $response
     */
    $response = $this->post(route('login.store'), [
        'email' => 'user@user.com',
        'password' => 'password',
    ]);
    $response->assertSessionDoesntHaveErrors();
    $response->assertSessionHas(['success' => 'Login successful']);
    $response->assertStatus(302);
    $response->assertRedirect(route('dashboard'));
});
it('permits logout', function () {
    $this->actingAs(\App\Models\User::factory()->create());
    /*
     * @var \Illuminate\Foundation\Testing\TestResponse $response
     */
    $response = $this->post(route('logout'));
    $response->assertSessionDoesntHaveErrors();
    $response->assertSessionHas(['success' => 'Logout successful']);
    $response->assertStatus(302);
    $response->assertRedirect(route('home'));
});

/** SIGNUP */
it('returns success on signup page', function () {
    $response = $this->get(route('signup'));

    $response->assertStatus(200);
    $response->assertSee('Signup');
    $response->assertSee('Click here to log in');
    $response->assertSee('<a href="' . route('login') . '"', false);
    $response->assertSee('Google');
    $response->assertSee('<a href="' . route('login.oauth', 'google') . '"', false);
    $response->assertSee('Facebook');
    $response->assertSee('<a href="' . route('login.oauth', 'facebook') . '"', false);
});
it('does not permit signup with existing email', function () {
    \App\Models\User::factory()->create([
        'email' => 'user@user.com',
        'password' => bcrypt('password'),
    ]);
    /*
     * @var \Illuminate\Foundation\Testing\TestResponse $response
     */
    $response = $this->post(route('signup.store'), [
        'name' => 'Test User',
        'email' => 'user@user.com',
        'password' => '//P@55w0rd',
        'password_confirmation' => '//P@55w0rd',
        'address_line1' => '123 Test St',
        'city' => 'Test City',
        'postcode' => '12345',
        'country_id' => 1,
        'role' => ['customer'],
    ]);
    $response->assertSessionHasErrors(['email']);
    $response->assertStatus(302);
});
it('does not permit signup with incorrect input data', function () {
    /*
     * @var \Illuminate\Foundation\Testing\TestResponse $response
     */
    $response = $this->post(route('signup.store'), [
        'name' => '',
        'email' => 'user@user,com',
        'password' => 'password',
        'password_confirmation' => 'pssword',
        'address_line1' => '',
        'city' => '',
        'postcode' => '',
        'country_id' => null,
        'role' => [],
    ]);
    // $response->ddSession();
    $response->assertInvalid([
        'name',
        'email',
        'password',
        'address_line1',
        'city',
        'postcode',
        'country_id',
        'role',
    ]);
});
it('permits signup with correct input data', function () {
    /*
     * @var \Illuminate\Foundation\Testing\TestResponse $response
     */
    $response = $this->post(route('signup.store'), [
        'name' => 'Test User',
        'email' => 'user@user.com',
        'password' => '//P@55w0rd',
        'password_confirmation' => '//P@55w0rd',
        'address_line1' => '123 Test St',
        'city' => 'Test City',
        'postcode' => '12345',
        'country_id' => 1,
        'role' => ['customer'],
    ]);
    $response->assertSessionDoesntHaveErrors();
    $response->assertSessionHas(['success' => 'Account created. Please check your email to verify your account.']);
    $response->assertStatus(302);
    $response->assertRedirect(route('home'));
});

/** PASSWORD RESET */
it('returns success on forgot password page', function () {
    $response = $this->get(route('password.reset-request'));

    $response->assertStatus(200);
    $response->assertSee('Reset Password');
    $response->assertSee('Submit');
    $response->assertSee('Log in here');
    $response->assertSee('<a href="' . route('login') . '"', false);
});
it('does not permit password reset with incorrect email', function () {
    /*
     * @var \Illuminate\Foundation\Testing\TestResponse $response
     */
    $response = $this->post(route('password.reset-email'), [
        'email' => 'user@user,com',
    ]);
    $response->assertSessionHasErrors(['email']);
    $response->assertStatus(302);
});
it('permits password reset with correct email', function () {
    \App\Models\User::factory()->create([
        'email' => 'user@user.com',
        'password' => bcrypt('password'),
    ]);
    /*
     * @var \Illuminate\Foundation\Testing\TestResponse $response
     */
    $response = $this->post(route('password.reset-email'), [
        'email' => 'user@user.com',
    ]);
    $response->assertSessionDoesntHaveErrors();
    $response->assertSessionHas(['success' => 'We have emailed your password reset link.']);
    $response->assertStatus(302);
    $response->assertRedirect(route('login'));
});
it('returns success on reset password page', function () {
    $token = \Illuminate\Support\Str::random(60);
    $response = $this->get(route('password.reset', ['token' => $token]));

    $response->assertStatus(200);
    $response->assertSee('Reset Password');
    $response->assertSee('Submit');
    $response->assertSee('Log in here');
    $response->assertSee('<a href="' . route('login') . '"', false);
});