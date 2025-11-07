<?php

namespace App\Services\AI\Providers;

use App\Services\AI\Contracts\AIProviderInterface;
use App\Services\AI\Contracts\AIResponse;
use OpenAI;

class OpenAIProvider implements AIProviderInterface
{
    private $client;
    private $model;
    private $maxTokens;
    private $temperature;
    private $lastTokenUsage = [];

    public function __construct()
    {
        $apiKey = config('ai.providers.openai.api_key');

        if (!$apiKey) {
            throw new \Exception('OpenAI API key not configured');
        }

        $this->client = OpenAI::client($apiKey);
        $this->model = config('ai.providers.openai.model', 'gpt-4o-mini');
        $this->maxTokens = config('ai.providers.openai.max_tokens', 2000);
        $this->temperature = config('ai.providers.openai.temperature', 0.7);
    }

    public function complete(string $prompt, array $options = []): AIResponse
    {
        $response = $this->client->completions()->create([
            'model' => $options['model'] ?? $this->model,
            'prompt' => $prompt,
            'max_tokens' => $options['max_tokens'] ?? $this->maxTokens,
            'temperature' => $options['temperature'] ?? $this->temperature,
        ]);

        $this->lastTokenUsage = [
            'prompt_tokens' => $response->usage->promptTokens ?? 0,
            'completion_tokens' => $response->usage->completionTokens ?? 0,
            'total_tokens' => $response->usage->totalTokens ?? 0,
        ];

        $cost = $this->calculateCost($this->lastTokenUsage);

        return new AIResponse(
            content: $response->choices[0]->text ?? '',
            provider: 'openai',
            metadata: ['model' => $response->model],
            promptTokens: $this->lastTokenUsage['prompt_tokens'],
            completionTokens: $this->lastTokenUsage['completion_tokens'],
            totalTokens: $this->lastTokenUsage['total_tokens'],
            cost: $cost
        );
    }

    public function chat(array $messages, array $options = []): AIResponse
    {
        $response = $this->client->chat()->create([
            'model' => $options['model'] ?? $this->model,
            'messages' => $messages,
            'max_tokens' => $options['max_tokens'] ?? $this->maxTokens,
            'temperature' => $options['temperature'] ?? $this->temperature,
        ]);

        $this->lastTokenUsage = [
            'prompt_tokens' => $response->usage->promptTokens ?? 0,
            'completion_tokens' => $response->usage->completionTokens ?? 0,
            'total_tokens' => $response->usage->totalTokens ?? 0,
        ];

        $cost = $this->calculateCost($this->lastTokenUsage);

        return new AIResponse(
            content: $response->choices[0]->message->content ?? '',
            provider: 'openai',
            metadata: [
                'model' => $response->model,
                'finish_reason' => $response->choices[0]->finishReason ?? null,
            ],
            promptTokens: $this->lastTokenUsage['prompt_tokens'],
            completionTokens: $this->lastTokenUsage['completion_tokens'],
            totalTokens: $this->lastTokenUsage['total_tokens'],
            cost: $cost
        );
    }

    public function getName(): string
    {
        return 'OpenAI';
    }

    public function isAvailable(): bool
    {
        return !empty(config('ai.providers.openai.api_key'));
    }

    public function getTokenUsage(): array
    {
        return $this->lastTokenUsage;
    }

    private function calculateCost(array $usage): float
    {
        $pricing = config("ai.pricing.openai.{$this->model}", ['input' => 0, 'output' => 0]);

        $inputCost = ($usage['prompt_tokens'] / 1_000_000) * $pricing['input'];
        $outputCost = ($usage['completion_tokens'] / 1_000_000) * $pricing['output'];

        return round($inputCost + $outputCost, 6);
    }
}
