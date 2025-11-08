<?php

namespace App\Services;

use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;

class ReplicateAIService
{
    protected $apiKey;
    protected $baseUrl = 'https://api.replicate.com/v1';
    protected $model;
    protected $modelVersion;

    public function __construct()
    {
        $this->apiKey = config('services.replicate.api_key');
        $this->model = config('services.replicate.model', 'meta/llama-2-70b-chat');
        $this->modelVersion = config('services.replicate.version');
    }

    /**
     * Generate text completion using Replicate API
     */
    public function complete(string $prompt, array $options = []): string
    {
        try {
            // Create prediction
            $response = Http::withHeaders([
                'Authorization' => 'Token ' . $this->apiKey,
                'Content-Type' => 'application/json',
            ])->timeout(120)->post($this->baseUrl . '/predictions', [
                'version' => $options['version'] ?? $this->modelVersion ?? $this->getLatestVersion($this->model),
                'input' => [
                    'prompt' => $prompt,
                    'max_new_tokens' => $options['max_tokens'] ?? 2000,
                    'temperature' => $options['temperature'] ?? 0.7,
                    'top_p' => $options['top_p'] ?? 0.9,
                    'repetition_penalty' => $options['repetition_penalty'] ?? 1,
                ],
            ]);

            if (!$response->successful()) {
                Log::error('Replicate API error', [
                    'status' => $response->status(),
                    'body' => $response->body()
                ]);
                throw new \Exception('Replicate API error: ' . $response->body());
            }

            $prediction = $response->json();

            // Poll for completion
            $output = $this->waitForPrediction($prediction['id']);

            // Join output array if needed
            if (is_array($output)) {
                return implode('', $output);
            }

            return $output;
        } catch (\Exception $e) {
            Log::error('Replicate Service Exception', ['message' => $e->getMessage()]);
            throw $e;
        }
    }

    /**
     * Wait for prediction to complete
     */
    protected function waitForPrediction(string $predictionId, int $maxAttempts = 30): string
    {
        $attempts = 0;

        while ($attempts < $maxAttempts) {
            $response = Http::withHeaders([
                'Authorization' => 'Token ' . $this->apiKey,
            ])->get($this->baseUrl . "/predictions/{$predictionId}");

            if (!$response->successful()) {
                throw new \Exception('Failed to fetch prediction status');
            }

            $prediction = $response->json();

            if ($prediction['status'] === 'succeeded') {
                return $prediction['output'];
            }

            if ($prediction['status'] === 'failed') {
                throw new \Exception('Prediction failed: ' . ($prediction['error'] ?? 'Unknown error'));
            }

            // Wait before next poll
            sleep(2);
            $attempts++;
        }

        throw new \Exception('Prediction timeout');
    }

    /**
     * Get latest version for a model
     */
    protected function getLatestVersion(string $model): string
    {
        try {
            $response = Http::withHeaders([
                'Authorization' => 'Token ' . $this->apiKey,
            ])->get($this->baseUrl . "/models/{$model}");

            if (!$response->successful()) {
                throw new \Exception('Failed to fetch model info');
            }

            $modelData = $response->json();
            return $modelData['latest_version']['id'] ?? throw new \Exception('No version found');
        } catch (\Exception $e) {
            Log::error('Failed to get Replicate model version', ['message' => $e->getMessage()]);
            // Fallback to known version for Llama 2 70B
            return '02e509c789964a7ea8736978a43525956ef40397be9033abf9fd2badfe68c9e3';
        }
    }

    /**
     * Generate exercise with Llama 2
     */
    public function generateExercise(string $materialText, string $difficulty = 'medium'): array
    {
        $prompt = "[INST] Based on the following educational material, create 1 multiple-choice question with 4 options.

Material:
{$materialText}

Difficulty: {$difficulty}

Respond ONLY with valid JSON in this exact format:
{
  \"question\": \"Question text here?\",
  \"options\": [\"Option A\", \"Option B\", \"Option C\", \"Option D\"],
  \"correct_answer\": \"Option A\",
  \"explanation\": \"Why this is correct\"
} [/INST]";

        $response = $this->complete($prompt, [
            'temperature' => 0.4, // Lower for more structured output
            'max_tokens' => 1000,
        ]);

        // Extract JSON from response
        preg_match('/\{[^{}]*(?:\{[^{}]*\}[^{}]*)*\}/s', $response, $matches);
        if (empty($matches)) {
            throw new \Exception('Failed to extract JSON from AI response');
        }

        $json = json_decode($matches[0], true);

        if (json_last_error() !== JSON_ERROR_NONE) {
            throw new \Exception('Failed to parse AI response as JSON');
        }

        return $json;
    }

    /**
     * Generate flashcards
     */
    public function generateFlashcards(string $topic, int $count = 5): array
    {
        $prompt = "[INST] Create {$count} educational flashcards about: {$topic}

Respond ONLY with valid JSON array:
[
  {\"front\": \"Question or term\", \"back\": \"Answer or definition\"},
  {\"front\": \"...\", \"back\": \"...\"}
] [/INST]";

        $response = $this->complete($prompt, [
            'temperature' => 0.5,
            'max_tokens' => 1500,
        ]);

        // Extract JSON array
        preg_match('/\[.*\]/s', $response, $matches);
        if (empty($matches)) {
            throw new \Exception('Failed to extract JSON array');
        }

        $json = json_decode($matches[0], true);

        if (!$json) {
            throw new \Exception('Failed to parse flashcards response');
        }

        return $json;
    }

    /**
     * Explain concept
     */
    public function explainConcept(string $concept, string $level = 'beginner'): string
    {
        $prompt = "[INST] Explain the following concept in a clear, educational way for a {$level} level student:

{$concept} [/INST]";

        return $this->complete($prompt, [
            'temperature' => 0.7,
            'max_tokens' => 1000,
        ]);
    }

    /**
     * Summarize material
     */
    public function summarizeMaterial(string $text, int $maxLength = 500): string
    {
        $prompt = "[INST] Summarize the following educational material in approximately {$maxLength} words. Focus on key concepts:

{$text} [/INST]";

        return $this->complete($prompt, [
            'temperature' => 0.4,
            'max_tokens' => $maxLength * 2,
        ]);
    }

    /**
     * Check if API is configured
     */
    public function isConfigured(): bool
    {
        return !empty($this->apiKey);
    }
}
