<?php

namespace App\Jobs;

use App\Models\Material;
use App\Services\OCR\OCRManager;
use App\Services\AI\AIManager;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Queue\Queueable;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Storage;

class ProcessMaterialWithOCR implements ShouldQueue
{
    use Queueable;

    public $timeout = 300;
    public $tries = 3;

    /**
     * Create a new job instance.
     */
    public function __construct(
        public Material $material
    ) {}

    /**
     * Execute the job.
     */
    public function handle(OCRManager $ocrManager, AIManager $aiManager): void
    {
        try {
            // Skip if already processed
            if ($this->material->is_processed) {
                return;
            }

            // Only process image and PDF types
            if (!in_array($this->material->type, ['image', 'pdf', 'document'])) {
                return;
            }

            $filePath = Storage::path($this->material->file_path);

            if (!file_exists($filePath)) {
                Log::error('Material file not found', ['material_id' => $this->material->id]);
                return;
            }

            // Extract text using OCR
            $ocrResponse = $ocrManager->extractText($filePath);

            if ($ocrResponse->isSuccessful()) {
                // Use AI to analyze and structure the extracted text
                $analysis = $aiManager->chat([
                    [
                        'role' => 'system',
                        'content' => 'You are an educational content analyzer. Extract key concepts, topics, and create a summary from the provided text.'
                    ],
                    [
                        'role' => 'user',
                        'content' => "Analyze this educational material and extract:\n1. Main topics\n2. Key concepts\n3. Brief summary\n\nText:\n{$ocrResponse->getText()}"
                    ]
                ], ['max_tokens' => 1000]);

                // Update material with extracted data
                $this->material->update([
                    'extracted_text' => $ocrResponse->getText(),
                    'ai_metadata' => [
                        'ocr_confidence' => $ocrResponse->getConfidence(),
                        'ocr_provider' => $ocrResponse->getProvider(),
                        'ocr_language' => $ocrResponse->getLanguage(),
                        'ai_analysis' => $analysis->getContent(),
                        'processed_at' => now()->toIso8601String(),
                    ],
                    'is_processed' => true,
                    'processed_at' => now(),
                ]);

                Log::info('Material processed successfully', [
                    'material_id' => $this->material->id,
                    'text_length' => strlen($ocrResponse->getText()),
                    'confidence' => $ocrResponse->getConfidence(),
                ]);
            }
        } catch (\Exception $e) {
            Log::error('Material processing failed', [
                'material_id' => $this->material->id,
                'error' => $e->getMessage(),
                'trace' => $e->getTraceAsString(),
            ]);

            throw $e;
        }
    }
}
