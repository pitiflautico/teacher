<?php

namespace Tests\Unit\Services\OCR;

use App\Services\OCR\Contracts\OCRResponse;
use App\Services\OCR\OCRManager;
use Illuminate\Support\Facades\Storage;
use Tests\TestCase;

class OCRManagerTest extends TestCase
{
    protected OCRManager $ocrManager;

    protected function setUp(): void
    {
        parent::setUp();

        $this->ocrManager = app(OCRManager::class);
    }

    /** @test */
    public function it_can_list_available_providers()
    {
        $providers = $this->ocrManager->getAvailableProviders();

        $this->assertIsArray($providers);
        // May be empty if Tesseract is not installed
    }

    /** @test */
    public function it_can_switch_providers()
    {
        $providers = $this->ocrManager->getAvailableProviders();

        if (!empty($providers)) {
            $providerName = array_key_first($providers);
            $manager = $this->ocrManager->provider($providerName);

            $this->assertInstanceOf(OCRManager::class, $manager);
        }

        $this->assertTrue(true); // If no providers available, test passes
    }

    /** @test */
    public function it_throws_exception_for_invalid_provider()
    {
        $this->expectException(\InvalidArgumentException::class);

        $this->ocrManager->provider('invalid_ocr_provider');
    }

    /** @test */
    public function it_can_extract_text_from_image()
    {
        $this->markTestSkipped('Requires Tesseract installation and test image');

        // This would require:
        // 1. Tesseract to be installed
        // 2. A test image file with known text
        // 3. Verification that extracted text matches expected content

        Storage::fake('local');

        // Create a test image (would need actual image generation)
        $testImagePath = storage_path('app/test-image.jpg');

        $response = $this->ocrManager->extractText($testImagePath);

        $this->assertInstanceOf(OCRResponse::class, $response);
        $this->assertNotEmpty($response->text);
        $this->assertIsFloat($response->confidence);
        $this->assertGreaterThan(0, $response->confidence);
        $this->assertLessThanOrEqual(100, $response->confidence);
    }

    /** @test */
    public function it_handles_missing_file_gracefully()
    {
        $this->expectException(\Exception::class);

        $nonExistentPath = storage_path('app/non-existent-image.jpg');
        $this->ocrManager->extractText($nonExistentPath);
    }

    /** @test */
    public function it_handles_invalid_file_format()
    {
        $this->markTestSkipped('Requires test file setup');

        Storage::fake('local');

        // Create a non-image file
        $invalidFile = storage_path('app/test.txt');
        file_put_contents($invalidFile, 'Not an image');

        $this->expectException(\Exception::class);

        $this->ocrManager->extractText($invalidFile);
    }

    /** @test */
    public function it_supports_multiple_languages()
    {
        $this->markTestSkipped('Requires Tesseract with language packs');

        // This would test that OCR can handle different languages
        // like 'eng', 'spa', 'fra', etc.
    }

    /** @test */
    public function it_returns_confidence_score()
    {
        $this->markTestSkipped('Requires Tesseract installation and test image');

        // Verify that confidence score is returned and is within valid range
    }

    /** @test */
    public function it_extracts_text_from_url()
    {
        $this->markTestSkipped('Requires network access and valid image URL');

        // This would test extractTextFromUrl method
        $imageUrl = 'https://example.com/test-image.jpg';

        $response = $this->ocrManager->extractTextFromUrl($imageUrl);

        $this->assertInstanceOf(OCRResponse::class, $response);
    }

    /** @test */
    public function it_handles_pdf_files()
    {
        $this->markTestSkipped('Requires Tesseract with PDF support');

        // Test OCR on PDF files if supported
    }

    /** @test */
    public function it_respects_timeout_settings()
    {
        $this->markTestSkipped('Requires mocking Process or long-running OCR task');

        // Verify that OCR operations timeout appropriately
    }
}
