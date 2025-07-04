<?php

namespace Database\Factories;

use Carbon\Carbon;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\ScheduledClass>
 */
class ScheduledClassFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        return [
            'instructor_id' => \App\Models\User::factory()->state(['role' => 'instructor']),
            'class_type_id' => \App\Models\ClassType::query()->inRandomOrder()->value('id') ?? 1,
            'scheduled_at' => Carbon::now()->addDays(rand(1, 10))->setTime(rand(8, 20), [0, 30][rand(0, 1)]), // Random date and time within the next 10 days, minutes 0 or 30 only
            'capacity' => $this->faker->numberBetween(5, 30),
            'status' => $this->faker->boolean(95) ? 'scheduled' : 'cancelled', // 95% chance of being 'scheduled', 5% chance of being 'cancelled'
            'location' => $this->faker->randomElement(['Gym A', 'Gym B', 'Gym C', 'Ground Floor', 'Second Floor', 'Outdoor Area', 'Studio 1', 'Studio 2']),
            'description' => $this->faker->boolean(20) ? $this->faker->sentence() : null, // Random, most of the time no desc.
            'created_at' => now(),
            'updated_at' => now(),
        ];
    }
}
