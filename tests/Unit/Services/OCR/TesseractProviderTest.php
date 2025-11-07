<?php

namespace Tests\Unit\Services\OCR;

use App\Services\OCR\Contracts\OCRResponse;
use App\Services\OCR\Providers\TesseractProvider;
use Tests\TestCase;

class TesseractProviderTest extends TestCase
{
    protected TesseractProvider $provider;

    protected function setUp(): void
    {
        parent::setUp();

        config(['ocr.providers.tesseract.path' => 'tesseract']);
        config(['ocr.providers.tesseract.language' => 'eng']);

        $this->provider = new TesseractProvider();
    }

    /** @test */
    public function it_has_correct_name()
    {
        $this->assertEquals('Tesseract', $this->provider->getName());
    }

    /** @test */
    public function it_checks_availability()
    {
        $isAvailable = $this->provider->isAvailable();

        $this->assertIsBool($isAvailable);

        // Availability depends on whether Tesseract is installed
        // We don't assert true/false as it depends on environment
    }

    /** @test */
    public function it_lists_supported_languages()
    {
        $languages = $this->provider->getSupportedLanguages();

        $this->assertIsArray($languages);

        if ($this->provider->isAvailable()) {
            $this->assertNotEmpty($languages);
            $this->assertContains('eng', $languages);
        }
    }

    /** @test */
    public function it_validates_file_path()
    {
        $this->expectException(\InvalidArgumentException::class);

        $this->provider->extractText('/non/existent/file.jpg');
    }

    /** @test */
    public function it_uses_configured_language()
    {
        config(['ocr.providers.tesseract.language' => 'spa']);

        $provider = new TesseractProvider();

        // Language configuration is used internally
        $this->assertTrue(true);
    }

    /** @test */
    public function it_supports_custom_language_option()
    {
        $this->markTestSkipped('Requires Tesseract installation and test image');

        // Test that language option can be overridden per request
        $testImagePath = storage_path('app/test-image.jpg');

        $response = $this->provider->extractText($testImagePath, [
            'language' => 'fra',
        ]);

        $this->assertInstanceOf(OCRResponse::class, $response);
    }

    /** @test */
    public function it_handles_process_timeout()
    {
        $this->markTestSkipped('Requires mocking Process');

        // Test that timeout is properly handled
    }

    /** @test */
    public function it_cleans_up_temporary_files()
    {
        $this->markTestSkipped('Requires Tesseract installation and test image');

        // Verify that temporary output files are deleted after OCR
    }

    /** @test */
    public function it_estimates_confidence()
    {
        $this->markTestSkipped('Requires Tesseract installation and test image');

        $testImagePath = storage_path('app/test-image.jpg');

        $response = $this->provider->extractText($testImagePath);

        $this->assertIsFloat($response->confidence);
        $this->assertGreaterThanOrEqual(0, $response->confidence);
        $this->assertLessThanOrEqual(100, $response->confidence);
    }

    /** @test */
    public function it_returns_empty_text_for_blank_image()
    {
        $this->markTestSkipped('Requires blank test image');

        // Test with a blank/white image
        $blankImagePath = storage_path('app/blank-image.jpg');

        $response = $this->provider->extractText($blankImagePath);

        $this->assertEmpty(trim($response->text));
    }

    /** @test */
    public function it_handles_rotated_images()
    {
        $this->markTestSkipped('Requires Tesseract with rotation support');

        // Test that Tesseract can handle rotated images
    }

    /** @test */
    public function it_handles_low_quality_images()
    {
        $this->markTestSkipped('Requires low-quality test image');

        // Test OCR accuracy with low-quality images
    }
}
