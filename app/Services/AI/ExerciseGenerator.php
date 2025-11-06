<?php

namespace App\Services\AI;

use App\Models\Exercise;
use App\Models\Material;
use App\Services\AI\AIManager;
use Illuminate\Support\Facades\Log;

class ExerciseGenerator
{
    public function __construct(
        private AIManager $aiManager
    ) {}

    /**
     * Generate exercises from material
     */
    public function generateFromMaterial(
        Material $material,
        string $type = 'multiple_choice',
        string $difficulty = 'medium',
        int $count = 5
    ): array {
        $context = $this->prepareContext($material);

        $prompt = $this->buildPrompt($context, $type, $difficulty, $count);

        $response = $this->aiManager->chat([
            ['role' => 'system', 'content' => 'You are an expert educational content creator. Generate high-quality exercises in JSON format.'],
            ['role' => 'user', 'content' => $prompt]
        ], ['max_tokens' => 2000]);

        $exercises = $this->parseExercises($response->getContent(), $material, $type, $difficulty);

        Log::info('Exercises generated', [
            'material_id' => $material->id,
            'type' => $type,
            'count' => count($exercises),
        ]);

        return $exercises;
    }

    /**
     * Generate exercises from topic
     */
    public function generateFromTopic(
        \App\Models\Topic $topic,
        string $type = 'multiple_choice',
        string $difficulty = 'medium',
        int $count = 5
    ): array {
        $materials = $topic->materials()->where('is_processed', true)->get();

        $allExercises = [];
        foreach ($materials as $material) {
            $exercises = $this->generateFromMaterial($material, $type, $difficulty, $count);
            $allExercises = array_merge($allExercises, $exercises);
        }

        return array_slice($allExercises, 0, $count);
    }

    private function prepareContext(Material $material): string
    {
        $context = "Title: {$material->title}\n\n";

        if ($material->description) {
            $context .= "Description: {$material->description}\n\n";
        }

        if ($material->extracted_text) {
            $context .= "Content:\n" . substr($material->extracted_text, 0, 2000);
        }

        return $context;
    }

    private function buildPrompt(string $context, string $type, string $difficulty, int $count): string
    {
        return <<<PROMPT
Generate {$count} {$difficulty} difficulty {$type} exercises based on the following educational material.

Material:
{$context}

Return a JSON array with this structure:
[
  {
    "question": "The question text",
    "options": ["Option A", "Option B", "Option C", "Option D"], // only for multiple_choice
    "correct_answers": ["correct option(s)"],
    "explanation": "Why this is the correct answer",
    "hints": "A helpful hint",
    "contains_math": false
  }
]

Requirements:
- Questions should be clear and educational
- For multiple_choice: provide 4 options
- For true_false: correct_answers should be ["true"] or ["false"]
- For short_answer: provide key acceptable answers
- Include detailed explanations
- Detect if math formulas are present (set contains_math: true)

Return ONLY the JSON array, no additional text.
PROMPT;
    }

    private function parseExercises(string $content, Material $material, string $type, string $difficulty): array
    {
        // Extract JSON from response
        $content = trim($content);
        if (str_starts_with($content, '```json')) {
            $content = substr($content, 7);
        }
        if (str_starts_with($content, '```')) {
            $content = substr($content, 3);
        }
        if (str_ends_with($content, '```')) {
            $content = substr($content, 0, -3);
        }

        try {
            $data = json_decode(trim($content), true);

            if (!is_array($data)) {
                throw new \Exception('Invalid JSON response');
            }

            $exercises = [];
            foreach ($data as $item) {
                $exercise = Exercise::create([
                    'user_id' => $material->user_id,
                    'subject_id' => $material->subject_id,
                    'topic_id' => $material->topic_id,
                    'material_id' => $material->id,
                    'title' => substr($item['question'], 0, 100),
                    'type' => $type,
                    'difficulty' => $difficulty,
                    'question' => $item['question'],
                    'options' => $item['options'] ?? null,
                    'correct_answers' => $item['correct_answers'],
                    'explanation' => $item['explanation'] ?? null,
                    'hints' => $item['hints'] ?? null,
                    'contains_math' => $item['contains_math'] ?? false,
                    'ai_metadata' => [
                        'provider' => 'ai_generated',
                        'generated_at' => now()->toIso8601String(),
                    ],
                ]);

                $exercises[] = $exercise;
            }

            return $exercises;
        } catch (\Exception $e) {
            Log::error('Failed to parse exercises', [
                'error' => $e->getMessage(),
                'content' => $content,
            ]);

            return [];
        }
    }
}
