<?php

namespace App\Services\OCR\Providers;

use App\Services\OCR\Contracts\OCRProviderInterface;
use App\Services\OCR\Contracts\OCRResponse;
use Symfony\Component\Process\Process;
use Symfony\Component\Process\Exception\ProcessFailedException;

class TesseractProvider implements OCRProviderInterface
{
    private string $tesseractPath;
    private string $language;

    public function __construct()
    {
        $this->tesseractPath = config('ocr.providers.tesseract.path', 'tesseract');
        $this->language = config('ocr.providers.tesseract.language', 'eng+spa');
    }

    public function extractText(string $filePath, array $options = []): OCRResponse
    {
        if (!file_exists($filePath)) {
            throw new \InvalidArgumentException("File not found: {$filePath}");
        }

        $language = $options['language'] ?? $this->language;
        $outputFile = tempnam(sys_get_temp_dir(), 'ocr_');

        $process = new Process([
            $this->tesseractPath,
            $filePath,
            $outputFile,
            '-l', $language,
            '--psm', '3', // Automatic page segmentation
            '--oem', '3', // LSTM OCR engine
        ]);

        $process->setTimeout(120);

        try {
            $process->mustRun();

            $text = file_get_contents($outputFile . '.txt');
            unlink($outputFile . '.txt');

            // Get confidence (approximate)
            $confidence = $this->estimateConfidence($text);

            return new OCRResponse(
                text: trim($text),
                provider: 'tesseract',
                confidence: $confidence,
                metadata: [
                    'language' => $language,
                    'psm' => 3,
                    'oem' => 3,
                ],
                language: $language
            );
        } catch (ProcessFailedException $exception) {
            throw new \RuntimeException('Tesseract OCR failed: ' . $exception->getMessage());
        }
    }

    public function extractTextFromUrl(string $url, array $options = []): OCRResponse
    {
        $tempFile = tempnam(sys_get_temp_dir(), 'ocr_download_');
        file_put_contents($tempFile, file_get_contents($url));

        try {
            $response = $this->extractText($tempFile, $options);
            return $response;
        } finally {
            if (file_exists($tempFile)) {
                unlink($tempFile);
            }
        }
    }

    public function getName(): string
    {
        return 'Tesseract';
    }

    public function isAvailable(): bool
    {
        $process = new Process([$this->tesseractPath, '--version']);
        $process->run();

        return $process->isSuccessful();
    }

    public function getSupportedLanguages(): array
    {
        return ['eng', 'spa', 'fra', 'deu', 'ita', 'por', 'rus', 'chi_sim', 'jpn', 'kor'];
    }

    /**
     * Estimate confidence based on text quality
     */
    private function estimateConfidence(string $text): float
    {
        if (empty($text)) {
            return 0.0;
        }

        // Simple heuristic: ratio of alphanumeric to total characters
        $alphanumeric = preg_match_all('/[a-zA-Z0-9]/', $text);
        $total = strlen($text);

        if ($total === 0) {
            return 0.0;
        }

        return min(($alphanumeric / $total) * 1.2, 1.0);
    }
}
