<?php

namespace App\Services;

use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;

class TogetherAIService
{
    protected $apiKey;
    protected $baseUrl = 'https://api.together.xyz/v1';
    protected $model;

    public function __construct()
    {
        $this->apiKey = config('services.together.api_key');
        $this->model = config('services.together.model', 'meta-llama/Meta-Llama-3.1-70B-Instruct-Turbo');
    }

    /**
     * Generate text completion
     */
    public function complete(string $prompt, array $options = []): string
    {
        try {
            $response = Http::withHeaders([
                'Authorization' => 'Bearer ' . $this->apiKey,
                'Content-Type' => 'application/json',
            ])->timeout(60)->post($this->baseUrl . '/chat/completions', [
                'model' => $options['model'] ?? $this->model,
                'messages' => [
                    ['role' => 'system', 'content' => $options['system'] ?? 'You are a helpful educational assistant.'],
                    ['role' => 'user', 'content' => $prompt],
                ],
                'max_tokens' => $options['max_tokens'] ?? 2000,
                'temperature' => $options['temperature'] ?? 0.7,
                'top_p' => $options['top_p'] ?? 0.9,
                'stop' => $options['stop'] ?? null,
            ]);

            if (!$response->successful()) {
                Log::error('Together.ai API error', [
                    'status' => $response->status(),
                    'body' => $response->body()
                ]);
                throw new \Exception('Together.ai API error: ' . $response->body());
            }

            return $response->json()['choices'][0]['message']['content'];
        } catch (\Exception $e) {
            Log::error('Together.ai Service Exception', ['message' => $e->getMessage()]);
            throw $e;
        }
    }

    /**
     * Generate exercise from material
     */
    public function generateExercise(string $materialText, string $difficulty = 'medium'): array
    {
        $prompt = "Based on the following educational material, create 1 multiple-choice question with 4 options.

Material:
{$materialText}

Difficulty: {$difficulty}

Format the response as JSON:
{
  \"question\": \"Question text here?\",
  \"options\": [\"Option A\", \"Option B\", \"Option C\", \"Option D\"],
  \"correct_answer\": \"Option A\",
  \"explanation\": \"Why this is correct\"
}";

        $response = $this->complete($prompt, [
            'system' => 'You are an expert educator creating high-quality assessment questions. Always respond with valid JSON only.',
            'temperature' => 0.5, // Lower for more consistent JSON
        ]);

        // Parse JSON response
        $json = json_decode($response, true);

        if (json_last_error() !== JSON_ERROR_NONE) {
            // Try to extract JSON from response if surrounded by text
            preg_match('/\{[^{}]*(?:\{[^{}]*\}[^{}]*)*\}/s', $response, $matches);
            if (!empty($matches)) {
                $json = json_decode($matches[0], true);
            }

            if (!$json || json_last_error() !== JSON_ERROR_NONE) {
                throw new \Exception('Failed to parse AI response as JSON');
            }
        }

        return $json;
    }

    /**
     * Generate flashcards from topic
     */
    public function generateFlashcards(string $topic, int $count = 5): array
    {
        $prompt = "Create {$count} educational flashcards about: {$topic}

Format as JSON array:
[
  {\"front\": \"Question or term\", \"back\": \"Answer or definition\"},
  {\"front\": \"...\", \"back\": \"...\"}
]";

        $response = $this->complete($prompt, [
            'system' => 'You are creating flashcards for spaced repetition study. Be concise and clear. Respond with valid JSON only.',
            'temperature' => 0.6,
            'max_tokens' => 1500,
        ]);

        $json = json_decode($response, true);

        if (json_last_error() !== JSON_ERROR_NONE) {
            // Try to extract JSON array
            preg_match('/\[.*\]/s', $response, $matches);
            if (!empty($matches)) {
                $json = json_decode($matches[0], true);
            }

            if (!$json) {
                throw new \Exception('Failed to parse flashcards response');
            }
        }

        return $json;
    }

    /**
     * Explain a concept
     */
    public function explainConcept(string $concept, string $level = 'beginner'): string
    {
        $prompt = "Explain the following concept in a clear, educational way for a {$level} level student:\n\n{$concept}";

        return $this->complete($prompt, [
            'system' => 'You are a patient, friendly teacher. Use simple language, examples, and analogies.',
            'temperature' => 0.7,
            'max_tokens' => 1000,
        ]);
    }

    /**
     * Generate study summary
     */
    public function summarizeMaterial(string $text, int $maxLength = 500): string
    {
        $prompt = "Summarize the following educational material in approximately {$maxLength} words. Focus on key concepts:\n\n{$text}";

        return $this->complete($prompt, [
            'system' => 'You create concise, accurate study summaries that highlight the most important information.',
            'temperature' => 0.4,
            'max_tokens' => $maxLength * 2,
        ]);
    }

    /**
     * List available models
     */
    public function listModels(): array
    {
        try {
            $response = Http::withHeaders([
                'Authorization' => 'Bearer ' . $this->apiKey,
            ])->get($this->baseUrl . '/models');

            if (!$response->successful()) {
                throw new \Exception('Failed to fetch models');
            }

            return $response->json()['data'] ?? [];
        } catch (\Exception $e) {
            Log::error('Failed to list Together.ai models', ['message' => $e->getMessage()]);
            return [];
        }
    }

    /**
     * Check if API is configured
     */
    public function isConfigured(): bool
    {
        return !empty($this->apiKey);
    }
}
