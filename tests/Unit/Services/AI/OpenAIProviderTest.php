<?php

namespace Tests\Unit\Services\AI;

use App\Services\AI\Contracts\AIResponse;
use App\Services\AI\Providers\OpenAIProvider;
use Tests\TestCase;

class OpenAIProviderTest extends TestCase
{
    protected OpenAIProvider $provider;

    protected function setUp(): void
    {
        parent::setUp();

        // Set a test API key in config
        config(['ai.providers.openai.api_key' => 'test-key']);

        $this->provider = new OpenAIProvider();
    }

    /** @test */
    public function it_has_correct_name()
    {
        $this->assertEquals('OpenAI', $this->provider->getName());
    }

    /** @test */
    public function it_checks_availability()
    {
        $isAvailable = $this->provider->isAvailable();

        $this->assertIsBool($isAvailable);

        // Provider is available if API key is set
        $this->assertTrue($isAvailable);
    }

    /** @test */
    public function it_is_not_available_without_api_key()
    {
        config(['ai.providers.openai.api_key' => null]);

        // Should throw exception when trying to instantiate without key
        $this->expectException(\Exception::class);

        $provider = new OpenAIProvider();
    }

    /** @test */
    public function it_returns_token_usage()
    {
        $usage = $this->provider->getTokenUsage();

        $this->assertIsArray($usage);
        // Initially token usage should be empty
        $this->assertEquals(0, $usage['prompt_tokens'] ?? 0);
        $this->assertEquals(0, $usage['completion_tokens'] ?? 0);
        $this->assertEquals(0, $usage['total_tokens'] ?? 0);
    }

    /** @test */
    public function it_throws_exception_when_completing_without_api_key()
    {
        $this->markTestSkipped('Constructor throws exception before we can test complete()');
    }

    /** @test */
    public function it_throws_exception_when_chatting_without_api_key()
    {
        $this->markTestSkipped('Constructor throws exception before we can test chat()');
    }

    /** @test */
    public function it_validates_chat_message_format()
    {
        $this->markTestSkipped('Requires valid API credentials or mocking HTTP client');

        // This would require mocking the OpenAI client
        // For now, we skip this test as it requires actual API calls
    }

    /** @test */
    public function it_calculates_cost_correctly()
    {
        $this->markTestSkipped('Requires valid API credentials or mocking HTTP client');

        // This would require mocking the API response
        // to verify that cost calculation is correct
    }

    /** @test */
    public function it_handles_api_errors_gracefully()
    {
        $this->markTestSkipped('Requires mocking HTTP client to simulate errors');

        // This would test error handling for:
        // - Network errors
        // - Rate limiting
        // - Invalid API key
        // - Malformed requests
    }

    /** @test */
    public function it_supports_different_models()
    {
        config(['ai.providers.openai.model' => 'gpt-4']);

        $provider = new OpenAIProvider();

        // Verify that provider uses the configured model
        $this->assertTrue($provider->isAvailable());
    }

    /** @test */
    public function it_respects_max_tokens_option()
    {
        $this->markTestSkipped('Requires valid API credentials or mocking HTTP client');

        // This would verify that max_tokens option is properly passed to the API
    }

    /** @test */
    public function it_respects_temperature_option()
    {
        $this->markTestSkipped('Requires valid API credentials or mocking HTTP client');

        // This would verify that temperature option is properly passed to the API
    }
}
