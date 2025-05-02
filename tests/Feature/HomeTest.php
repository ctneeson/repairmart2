<?php

it('displays "There are no listings published yet." on Home when there are no listings', function () {
    $response = $this->get(route('home'));
    $response->assertStatus(200);
    $response->assertSee('There are no listings published yet');
});
it('displays listings on Home when there are listings', function () {
    $this->seed();
    /*
     * @var \Illuminate\Foundation\Testing\TestResponse $response
     */
    $response = $this->get(route('home'));
    $response->assertStatus(200);
    $response->assertDontSee('There are no listings published yet');
    $response->assertViewHas('listings', function ($listings) {
        dump($listings->count());
        return $listings->count() == 5;
    });
});