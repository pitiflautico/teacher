<?php

namespace App\Services\OCR\Contracts;

class OCRResponse
{
    public function __construct(
        public readonly string $text,
        public readonly string $provider,
        public readonly float $confidence,
        public readonly array $metadata = [],
        public readonly ?string $language = null,
        public readonly ?array $blocks = null,
    ) {}

    /**
     * Get the extracted text
     */
    public function getText(): string
    {
        return $this->text;
    }

    /**
     * Get the provider name
     */
    public function getProvider(): string
    {
        return $this->provider;
    }

    /**
     * Get the confidence score (0-1)
     */
    public function getConfidence(): float
    {
        return $this->confidence;
    }

    /**
     * Check if extraction was successful
     */
    public function isSuccessful(): bool
    {
        return !empty($this->text) && $this->confidence > 0.5;
    }

    /**
     * Get detected language
     */
    public function getLanguage(): ?string
    {
        return $this->language;
    }

    /**
     * Get text blocks with positions
     */
    public function getBlocks(): ?array
    {
        return $this->blocks;
    }

    /**
     * Convert to array
     */
    public function toArray(): array
    {
        return [
            'text' => $this->text,
            'provider' => $this->provider,
            'confidence' => $this->confidence,
            'language' => $this->language,
            'metadata' => $this->metadata,
            'blocks_count' => $this->blocks ? count($this->blocks) : 0,
        ];
    }
}
