<?php

namespace Database\Factories;

use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\Subject>
 */
class SubjectFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        return [
            'user_id' => \App\Models\User::factory(),
            'name' => fake()->unique()->randomElement([
                'Mathematics',
                'Physics',
                'Chemistry',
                'Biology',
                'Computer Science',
                'History',
                'Geography',
                'Literature',
                'Philosophy',
                'Economics',
            ]),
            'description' => fake()->paragraph(),
            'color' => fake()->hexColor(),
            'icon' => fake()->optional()->word(),
        ];
    }
}
