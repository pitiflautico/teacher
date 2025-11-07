<?php

namespace Database\Factories;

use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\TokenUsage>
 */
class TokenUsageFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        $provider = fake()->randomElement(['openai', 'replicate', 'together']);
        $model = match($provider) {
            'openai' => 'gpt-4o-mini',
            'replicate' => 'meta/llama-2-70b-chat',
            'together' => 'meta-llama/Meta-Llama-3.1-8B-Instruct-Turbo',
        };

        $promptTokens = fake()->numberBetween(100, 2000);
        $completionTokens = fake()->numberBetween(50, 1000);
        $totalTokens = $promptTokens + $completionTokens;

        // Simplified cost calculation (in reality this comes from config)
        $cost = match($provider) {
            'openai' => ($promptTokens * 0.15 + $completionTokens * 0.60) / 1000000,
            'replicate' => $totalTokens * 0.65 / 1000000,
            'together' => ($promptTokens * 0.18 + $completionTokens * 0.18) / 1000000,
        };

        return [
            'user_id' => fake()->optional()->passthrough(\App\Models\User::factory()),
            'provider' => $provider,
            'model' => $model,
            'type' => fake()->randomElement(['completion', 'chat']),
            'prompt_tokens' => $promptTokens,
            'completion_tokens' => $completionTokens,
            'total_tokens' => $totalTokens,
            'cost' => round($cost, 6),
            'input_preview' => fake()->optional()->text(200),
            'output_preview' => fake()->optional()->text(200),
            'metadata' => null,
        ];
    }
}
