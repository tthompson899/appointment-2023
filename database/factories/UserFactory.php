<?php

namespace Database\Factories;

use Carbon\Carbon;
use Illuminate\Database\Eloquent\Factories\Factory;
use Illuminate\Support\Str;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\User>
 */
class UserFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        return [
            'name' => $name = $this->faker->firstName . ' ' . $this->faker->lastName,
            'email' => strtolower(str_replace(' ', '.', $name)) . '@' . $this->faker->safeEmailDomain(),
            'phone' => $this->faker->phoneNumber,
            'date_of_birth' => $this->faker->dateTimeBetween('-80 years', '-2 years', 'America/Chicago'),
            'created_at' => Carbon::now('America/Chicago'),
            'updated_at' => Carbon::now('America/Chicago')
        ];
    }

    /**
     * Indicate that the model's email address should be unverified.
     */
    public function unverified(): static
    {
        return $this->state(fn (array $attributes) => [
            'email_verified_at' => null,
        ]);
    }
}
