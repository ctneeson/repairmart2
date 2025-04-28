<?php

namespace Database\Factories;

use Illuminate\Database\Eloquent\Factories\Factory;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Str;
use App\Models\Country;
use App\Models\Role;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\User>
 */
class UserFactory extends Factory
{
    /**
     * The current password being used by the factory.
     */
    protected static ?string $password;

    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        return [
            'name' => fake()->name(),
            'email' => fake()->unique()->safeEmail(),
            'email_verified_at' => now(),
            'phone' => fake()->numerify('###########'),
            'password' => static::$password ??= Hash::make('password'),
            'google_id' => null,
            'facebook_id' => null,
            'address_line1' => $this->faker->streetAddress,
            'address_line2' => $this->faker->secondaryAddress,
            'city' => $this->faker->city,
            'postcode' => $this->faker->postcode,
            'country_id' => Country::inRandomOrder()->first()->id,
            'remember_token' => Str::random(10),
        ];
    }

    /**
     * Indicate that the model's email address should be unverified.
     */
    public function unverified(): static
    {
        return $this->state(fn(array $attributes) => [
            'email_verified_at' => null,
        ]);
    }

    /**
     * Indicate that the model's email address should be verified.
     */
    public function verified(): static
    {
        return $this->state(fn(array $attributes) => [
            'email_verified_at' => now(),
        ]);
    }

    /**
     * Indicate that the model should be a customer.
     */
    public function customer(): static
    {
        return $this->afterCreating(function ($user) {
            $customerRole = Role::where('name', 'customer')->first();
            $user->roles()->attach($customerRole);
        });
    }

    /**
     * Indicate that the model should be a specialist.
     */
    public function specialist(): static
    {
        return $this->afterCreating(function ($user) {
            $specialistRole = Role::where('name', 'specialist')->first();
            $user->roles()->attach($specialistRole);
        });
    }

    /**
     * Indicate that the model should be a admin.
     */
    public function admin(): static
    {
        return $this->afterCreating(function ($user) {
            $adminRole = Role::where('name', 'admin')->first();
            $user->roles()->attach($adminRole);
        });
    }
}
