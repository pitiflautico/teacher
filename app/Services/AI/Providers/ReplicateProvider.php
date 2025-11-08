<?php

namespace App\Services\AI\Providers;

use App\Services\AI\Contracts\AIProviderInterface;
use App\Services\AI\Contracts\AIResponse;
use GuzzleHttp\Client;

class ReplicateProvider implements AIProviderInterface
{
    private $client;
    private $apiKey;
    private $model;
    private $maxTokens;
    private $temperature;
    private $lastTokenUsage = [];

    public function __construct(?string $apiKey = null)
    {
        // Use provided API key or fall back to config
        $this->apiKey = $apiKey ?? config('ai.providers.replicate.api_key');

        if (!$this->apiKey) {
            throw new \Exception('Replicate API key not configured');
        }

        $this->client = new Client([
            'base_uri' => 'https://api.replicate.com/v1/',
            'headers' => [
                'Authorization' => 'Token ' . $this->apiKey,
                'Content-Type' => 'application/json',
            ],
        ]);

        $this->model = config('ai.providers.replicate.model', 'meta/llama-2-70b-chat');
        $this->maxTokens = config('ai.providers.replicate.max_tokens', 2000);
        $this->temperature = config('ai.providers.replicate.temperature', 0.7);
    }

    public function complete(string $prompt, array $options = []): AIResponse
    {
        return $this->chat([
            ['role' => 'user', 'content' => $prompt]
        ], $options);
    }

    public function chat(array $messages, array $options = []): AIResponse
    {
        $prompt = $this->formatMessages($messages);

        $response = $this->client->post('predictions', [
            'json' => [
                'version' => $this->getModelVersion($options['model'] ?? $this->model),
                'input' => [
                    'prompt' => $prompt,
                    'max_new_tokens' => $options['max_tokens'] ?? $this->maxTokens,
                    'temperature' => $options['temperature'] ?? $this->temperature,
                ],
            ],
        ]);

        $data = json_decode($response->getBody()->getContents(), true);

        // Wait for completion
        $predictionUrl = $data['urls']['get'];
        $result = $this->waitForCompletion($predictionUrl);

        // Estimate tokens
        $promptTokens = $this->estimateTokens($prompt);
        $completionTokens = $this->estimateTokens($result);
        $totalTokens = $promptTokens + $completionTokens;

        $this->lastTokenUsage = [
            'prompt_tokens' => $promptTokens,
            'completion_tokens' => $completionTokens,
            'total_tokens' => $totalTokens,
        ];

        $cost = $this->calculateCost($this->lastTokenUsage);

        return new AIResponse(
            content: $result,
            provider: 'replicate',
            metadata: ['model' => $this->model],
            promptTokens: $promptTokens,
            completionTokens: $completionTokens,
            totalTokens: $totalTokens,
            cost: $cost
        );
    }

    public function getName(): string
    {
        return 'Replicate';
    }

    public function isAvailable(): bool
    {
        return !empty(config('ai.providers.replicate.api_key'));
    }

    public function getTokenUsage(): array
    {
        return $this->lastTokenUsage;
    }

    private function getModelVersion(string $model): string
    {
        // Map model names to versions
        $versions = [
            'meta/llama-2-70b-chat' => '02e509c789964a7ea8736978a43525956ef40397be9033abf9fd2badfe68c9e3',
        ];

        return $versions[$model] ?? $versions['meta/llama-2-70b-chat'];
    }

    private function formatMessages(array $messages): string
    {
        $formatted = '';
        foreach ($messages as $message) {
            $role = $message['role'] === 'user' ? 'User' : 'Assistant';
            $formatted .= "{$role}: {$message['content']}\n";
        }
        return $formatted . "Assistant: ";
    }

    private function waitForCompletion(string $url, int $maxAttempts = 60): string
    {
        for ($i = 0; $i < $maxAttempts; $i++) {
            $response = $this->client->get($url);
            $data = json_decode($response->getBody()->getContents(), true);

            if ($data['status'] === 'succeeded') {
                return is_array($data['output']) ? implode('', $data['output']) : $data['output'];
            }

            if ($data['status'] === 'failed') {
                throw new \Exception('Replicate prediction failed: ' . ($data['error'] ?? 'Unknown error'));
            }

            sleep(2);
        }

        throw new \Exception('Replicate prediction timeout');
    }

    private function estimateTokens(string $text): int
    {
        // Rough estimation: ~4 characters per token
        return (int) ceil(strlen($text) / 4);
    }

    private function calculateCost(array $usage): float
    {
        $pricing = config("ai.pricing.replicate.{$this->model}", ['input' => 0, 'output' => 0]);

        $inputCost = ($usage['prompt_tokens'] / 1_000_000) * $pricing['input'];
        $outputCost = ($usage['completion_tokens'] / 1_000_000) * $pricing['output'];

        return round($inputCost + $outputCost, 6);
    }
}
