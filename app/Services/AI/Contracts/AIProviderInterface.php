<?php

namespace App\Services\AI\Contracts;

interface AIProviderInterface
{
    /**
     * Generate text completion from a prompt
     *
     * @param string $prompt
     * @param array $options
     * @return AIResponse
     */
    public function complete(string $prompt, array $options = []): AIResponse;

    /**
     * Generate chat completion from messages
     *
     * @param array $messages
     * @param array $options
     * @return AIResponse
     */
    public function chat(array $messages, array $options = []): AIResponse;

    /**
     * Get the provider name
     *
     * @return string
     */
    public function getName(): string;

    /**
     * Check if the provider is available
     *
     * @return bool
     */
    public function isAvailable(): bool;

    /**
     * Get token usage for last request
     *
     * @return array
     */
    public function getTokenUsage(): array;
}
