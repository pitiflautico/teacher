<?php

namespace Database\Factories;

use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\Material>
 */
class MaterialFactory extends Factory
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
            'subject_id' => \App\Models\Subject::factory(),
            'topic_id' => \App\Models\Topic::factory(),
            'title' => fake()->sentence(),
            'description' => fake()->paragraph(),
            'type' => fake()->randomElement(['document', 'image', 'pdf', 'link', 'note']),
            'file_path' => fake()->optional()->filePath(),
            'original_filename' => fake()->optional()->word() . '.pdf',
            'mime_type' => fake()->optional()->mimeType(),
            'file_size' => fake()->optional()->numberBetween(1000, 10000000),
            'extracted_text' => fake()->optional()->paragraphs(3, true),
            'ai_metadata' => null,
            'is_processed' => fake()->boolean(50),
            'processed_at' => fake()->optional()->dateTimeBetween('-1 month', 'now'),
        ];
    }
}
