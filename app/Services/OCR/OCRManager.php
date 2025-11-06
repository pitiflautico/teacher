<?php

namespace App\Services\OCR;

use App\Services\OCR\Contracts\OCRProviderInterface;
use App\Services\OCR\Contracts\OCRResponse;
use App\Services\OCR\Providers\TesseractProvider;
use Illuminate\Support\Facades\Log;

class OCRManager
{
    private array $providers = [];
    private ?OCRProviderInterface $currentProvider = null;

    public function __construct()
    {
        $this->registerProviders();
        $this->setDefaultProvider();
    }

    /**
     * Extract text from image
     */
    public function extractText(string $filePath, array $options = []): OCRResponse
    {
        $provider = $this->getProvider($options['provider'] ?? null);

        try {
            $response = $provider->extractText($filePath, $options);

            Log::info('OCR extraction completed', [
                'provider' => $provider->getName(),
                'file' => basename($filePath),
                'confidence' => $response->confidence,
                'text_length' => strlen($response->text),
            ]);

            return $response;
        } catch (\Exception $e) {
            Log::error('OCR extraction failed', [
                'provider' => $provider->getName(),
                'file' => $filePath,
                'error' => $e->getMessage(),
            ]);

            throw $e;
        }
    }

    /**
     * Extract text from URL
     */
    public function extractTextFromUrl(string $url, array $options = []): OCRResponse
    {
        $provider = $this->getProvider($options['provider'] ?? null);

        try {
            return $provider->extractTextFromUrl($url, $options);
        } catch (\Exception $e) {
            Log::error('OCR URL extraction failed', [
                'provider' => $provider->getName(),
                'url' => $url,
                'error' => $e->getMessage(),
            ]);

            throw $e;
        }
    }

    /**
     * Set the current provider
     */
    public function provider(string $name): self
    {
        if (!isset($this->providers[$name])) {
            throw new \InvalidArgumentException("OCR Provider {$name} not found");
        }

        $this->currentProvider = $this->providers[$name];
        return $this;
    }

    /**
     * Get available providers
     */
    public function getAvailableProviders(): array
    {
        return array_filter($this->providers, fn($provider) => $provider->isAvailable());
    }

    /**
     * Register all providers
     */
    private function registerProviders(): void
    {
        try {
            $this->providers['tesseract'] = new TesseractProvider();
        } catch (\Exception $e) {
            Log::warning('Tesseract provider not available: ' . $e->getMessage());
        }
    }

    /**
     * Set default provider
     */
    private function setDefaultProvider(): void
    {
        $defaultProvider = config('ocr.default', 'tesseract');

        if (isset($this->providers[$defaultProvider])) {
            $this->currentProvider = $this->providers[$defaultProvider];
        } else {
            // Fallback to first available provider
            $available = $this->getAvailableProviders();
            if (!empty($available)) {
                $this->currentProvider = reset($available);
            }
        }
    }

    /**
     * Get provider instance
     */
    private function getProvider(?string $name = null): OCRProviderInterface
    {
        if ($name) {
            return $this->providers[$name] ?? throw new \InvalidArgumentException("Provider {$name} not found");
        }

        if (!$this->currentProvider) {
            throw new \RuntimeException('No OCR provider available');
        }

        return $this->currentProvider;
    }
}
