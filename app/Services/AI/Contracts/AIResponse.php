<?php

namespace App\Services\AI\Contracts;

class AIResponse
{
    public function __construct(
        public readonly string $content,
        public readonly string $provider,
        public readonly array $metadata = [],
        public readonly ?int $promptTokens = null,
        public readonly ?int $completionTokens = null,
        public readonly ?int $totalTokens = null,
        public readonly ?float $cost = null,
    ) {}

    /**
     * Get the response content
     */
    public function getContent(): string
    {
        return $this->content;
    }

    /**
     * Get the provider name
     */
    public function getProvider(): string
    {
        return $this->provider;
    }

    /**
     * Get token usage information
     */
    public function getTokenUsage(): array
    {
        return [
            'prompt_tokens' => $this->promptTokens,
            'completion_tokens' => $this->completionTokens,
            'total_tokens' => $this->totalTokens,
        ];
    }

    /**
     * Get the estimated cost
     */
    public function getCost(): ?float
    {
        return $this->cost;
    }

    /**
     * Get all metadata
     */
    public function getMetadata(): array
    {
        return $this->metadata;
    }

    /**
     * Convert to array
     */
    public function toArray(): array
    {
        return [
            'content' => $this->content,
            'provider' => $this->provider,
            'metadata' => $this->metadata,
            'token_usage' => $this->getTokenUsage(),
            'cost' => $this->cost,
        ];
    }
}
