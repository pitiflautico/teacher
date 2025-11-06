<?php

namespace Database\Factories;

use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\Exercise>
 */
class ExerciseFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        $type = fake()->randomElement(['multiple_choice', 'true_false', 'short_answer', 'essay', 'problem_solving']);

        $options = null;
        if ($type === 'multiple_choice') {
            $options = [
                'A' => fake()->sentence(),
                'B' => fake()->sentence(),
                'C' => fake()->sentence(),
                'D' => fake()->sentence(),
            ];
        }

        $correctAnswers = match($type) {
            'multiple_choice' => [fake()->randomElement(['A', 'B', 'C', 'D'])],
            'true_false' => [fake()->randomElement(['true', 'false'])],
            'short_answer' => [fake()->word()],
            'essay' => ['Sample answer'],
            'problem_solving' => ['Solution steps'],
        };

        return [
            'user_id' => \App\Models\User::factory(),
            'subject_id' => \App\Models\Subject::factory(),
            'topic_id' => \App\Models\Topic::factory(),
            'material_id' => fake()->optional()->passthrough(\App\Models\Material::factory()),
            'title' => fake()->sentence(),
            'description' => fake()->optional()->paragraph(),
            'type' => $type,
            'difficulty' => fake()->randomElement(['easy', 'medium', 'hard']),
            'question' => fake()->paragraph() . '?',
            'options' => $options,
            'correct_answers' => $correctAnswers,
            'explanation' => fake()->optional()->paragraph(),
            'hints' => fake()->optional()->sentence(),
            'contains_math' => fake()->boolean(30),
            'ai_metadata' => null,
            'points' => fake()->numberBetween(5, 50),
            'time_limit' => fake()->optional()->numberBetween(60, 3600),
            'is_active' => fake()->boolean(80),
        ];
    }
}
