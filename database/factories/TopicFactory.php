<?php

namespace Database\Factories;

use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\Topic>
 */
class TopicFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        return [
            'subject_id' => \App\Models\Subject::factory(),
            'name' => fake()->sentence(3),
            'description' => fake()->paragraph(),
            'order' => fake()->numberBetween(1, 100),
            'is_completed' => fake()->boolean(20), // 20% chance of being completed
        ];
    }
}
