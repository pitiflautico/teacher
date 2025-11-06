<?php

namespace App\Services\AI\Providers;

use App\Services\AI\Contracts\AIProviderInterface;
use App\Services\AI\Contracts\AIResponse;
use GuzzleHttp\Client;

class TogetherProvider implements AIProviderInterface
{
    private $client;
    private $apiKey;
    private $model;
    private $maxTokens;
    private $temperature;
    private $lastTokenUsage = [];

    public function __construct()
    {
        $this->apiKey = config('ai.providers.together.api_key');

        if (!$this->apiKey) {
            throw new \Exception('Together API key not configured');
        }

        $this->client = new Client([
            'base_uri' => 'https://api.together.xyz/v1/',
            'headers' => [
                'Authorization' => 'Bearer ' . $this->apiKey,
                'Content-Type' => 'application/json',
            ],
        ]);

        $this->model = config('ai.providers.together.model', 'meta-llama/Meta-Llama-3.1-8B-Instruct-Turbo');
        $this->maxTokens = config('ai.providers.together.max_tokens', 2000);
        $this->temperature = config('ai.providers.together.temperature', 0.7);
    }

    public function complete(string $prompt, array $options = []): AIResponse
    {
        return $this->chat([
            ['role' => 'user', 'content' => $prompt]
        ], $options);
    }

    public function chat(array $messages, array $options = []): AIResponse
    {
        $response = $this->client->post('chat/completions', [
            'json' => [
                'model' => $options['model'] ?? $this->model,
                'messages' => $messages,
                'max_tokens' => $options['max_tokens'] ?? $this->maxTokens,
                'temperature' => $options['temperature'] ?? $this->temperature,
            ],
        ]);

        $data = json_decode($response->getBody()->getContents(), true);

        $this->lastTokenUsage = [
            'prompt_tokens' => $data['usage']['prompt_tokens'] ?? 0,
            'completion_tokens' => $data['usage']['completion_tokens'] ?? 0,
            'total_tokens' => $data['usage']['total_tokens'] ?? 0,
        ];

        $cost = $this->calculateCost($this->lastTokenUsage);

        return new AIResponse(
            content: $data['choices'][0]['message']['content'] ?? '',
            provider: 'together',
            metadata: [
                'model' => $data['model'] ?? $this->model,
                'finish_reason' => $data['choices'][0]['finish_reason'] ?? null,
            ],
            promptTokens: $this->lastTokenUsage['prompt_tokens'],
            completionTokens: $this->lastTokenUsage['completion_tokens'],
            totalTokens: $this->lastTokenUsage['total_tokens'],
            cost: $cost
        );
    }

    public function getName(): string
    {
        return 'Together';
    }

    public function isAvailable(): bool
    {
        return !empty(config('ai.providers.together.api_key'));
    }

    public function getTokenUsage(): array
    {
        return $this->lastTokenUsage;
    }

    private function calculateCost(array $usage): float
    {
        $pricing = config("ai.pricing.together.{$this->model}", ['input' => 0.18, 'output' => 0.18]);

        $inputCost = ($usage['prompt_tokens'] / 1_000_000) * $pricing['input'];
        $outputCost = ($usage['completion_tokens'] / 1_000_000) * $pricing['output'];

        return round($inputCost + $outputCost, 6);
    }
}
