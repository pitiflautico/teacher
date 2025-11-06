<?php

namespace Database\Factories;

use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\ExerciseAttempt>
 */
class ExerciseAttemptFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        $maxScore = fake()->numberBetween(10, 50);
        $isCorrect = fake()->boolean(70);
        $score = $isCorrect ? $maxScore : fake()->numberBetween(0, $maxScore - 1);

        $startedAt = fake()->dateTimeBetween('-1 month', 'now');
        $completedAt = fake()->dateTimeBetween($startedAt, 'now');
        $timeTaken = $completedAt->getTimestamp() - $startedAt->getTimestamp();

        return [
            'exercise_id' => \App\Models\Exercise::factory(),
            'user_id' => \App\Models\User::factory(),
            'user_answers' => [fake()->word()],
            'is_correct' => $isCorrect,
            'score' => $score,
            'max_score' => $maxScore,
            'accuracy_percentage' => round(($score / $maxScore) * 100, 2),
            'time_taken' => $timeTaken,
            'ai_feedback' => fake()->optional()->paragraph(),
            'started_at' => $startedAt,
            'completed_at' => $completedAt,
        ];
    }
}
