<?php

namespace App\Services\OCR\Contracts;

interface OCRProviderInterface
{
    /**
     * Extract text from image file
     *
     * @param string $filePath
     * @param array $options
     * @return OCRResponse
     */
    public function extractText(string $filePath, array $options = []): OCRResponse;

    /**
     * Extract text from image URL
     *
     * @param string $url
     * @param array $options
     * @return OCRResponse
     */
    public function extractTextFromUrl(string $url, array $options = []): OCRResponse;

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
     * Get supported languages
     *
     * @return array
     */
    public function getSupportedLanguages(): array;
}
