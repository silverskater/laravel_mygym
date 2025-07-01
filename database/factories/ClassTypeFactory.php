<?php

namespace Database\Factories;

use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\ClassType>
 */
class ClassTypeFactory extends Factory
{
    public function definition(): array
    {
        return [
            'name' => $this->faker->unique()->words(2, true),
            'description' => $this->faker->sentence(),
            'duration' => $this->faker->randomElement([30, 45, 60]),
            'capacity' => $this->faker->numberBetween(10, 30),
            'level' => $this->faker->randomElement(['beginner', 'intermediate', 'advanced', 'all']),
            'status' => $this->faker->randomElement(['active', 'inactive']),
            'color' => $this->faker->hexColor(),
            'image' => null,
        ];
    }
}
