<?php

namespace App\Services\AI;

use App\Services\AI\Contracts\AIProviderInterface;
use App\Services\AI\Contracts\AIResponse;
use App\Services\AI\Providers\OpenAIProvider;
use App\Services\AI\Providers\ReplicateProvider;
use App\Services\AI\Providers\TogetherProvider;
use App\Models\TokenUsage;
use App\Models\User;
use App\Models\UserAiProvider;
use Illuminate\Support\Facades\Log;

class AIManager
{
    private array $providers = [];
    private ?AIProviderInterface $currentProvider = null;
    private ?User $user = null;
    private ?UserAiProvider $userProvider = null;

    public function __construct(?User $user = null)
    {
        $this->user = $user ?? auth()->user();
        $this->registerProviders();
        $this->setDefaultProvider();
    }

    /**
     * Get completion from AI
     */
    public function complete(string $prompt, array $options = []): AIResponse
    {
        $provider = $this->getProvider($options['provider'] ?? null);

        // Check user limits if user provider is configured
        if ($this->userProvider && !$this->userProvider->canUse()) {
            throw new \RuntimeException('Usage limit reached for this provider. Please check your limits in AI Provider Settings.');
        }

        try {
            $response = $provider->complete($prompt, $options);

            $this->trackUsage($response, 'completion', $prompt);

            return $response;
        } catch (\Exception $e) {
            Log::error('AI completion failed', [
                'provider' => $provider->getName(),
                'error' => $e->getMessage(),
            ]);

            throw $e;
        }
    }

    /**
     * Get chat completion from AI
     */
    public function chat(array $messages, array $options = []): AIResponse
    {
        $provider = $this->getProvider($options['provider'] ?? null);

        // Check user limits if user provider is configured
        if ($this->userProvider && !$this->userProvider->canUse()) {
            throw new \RuntimeException('Usage limit reached for this provider. Please check your limits in AI Provider Settings.');
        }

        try {
            $response = $provider->chat($messages, $options);

            $this->trackUsage($response, 'chat', json_encode($messages));

            return $response;
        } catch (\Exception $e) {
            Log::error('AI chat failed', [
                'provider' => $provider->getName(),
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
            throw new \InvalidArgumentException("Provider {$name} not found");
        }

        $this->currentProvider = $this->providers[$name];

        // Set user provider if user is authenticated
        if ($this->user) {
            $this->userProvider = $this->user->getActiveAiProvider($name);
        }

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
     * Check monthly limit
     */
    public function checkMonthlyLimit(): array
    {
        $limit = config('ai.monthly_limit');

        if (!$limit) {
            return ['limited' => false, 'usage' => 0, 'limit' => null];
        }

        $usage = TokenUsage::whereMonth('created_at', now()->month)
            ->whereYear('created_at', now()->year)
            ->sum('total_tokens');

        return [
            'limited' => $usage >= $limit,
            'usage' => $usage,
            'limit' => $limit,
            'percentage' => round(($usage / $limit) * 100, 2),
        ];
    }

    /**
     * Get usage statistics
     */
    public function getUsageStats(int $days = 30): array
    {
        $stats = TokenUsage::where('created_at', '>=', now()->subDays($days))
            ->selectRaw('
                SUM(prompt_tokens) as total_prompt_tokens,
                SUM(completion_tokens) as total_completion_tokens,
                SUM(total_tokens) as total_tokens,
                SUM(cost) as total_cost,
                COUNT(*) as total_requests,
                provider
            ')
            ->groupBy('provider')
            ->get();

        return $stats->toArray();
    }

    /**
     * Register all providers
     */
    private function registerProviders(): void
    {
        // If user is authenticated, try to load user-specific providers first
        if ($this->user) {
            $userProviders = $this->user->aiProviders()
                ->where('is_active', true)
                ->get();

            foreach ($userProviders as $userProvider) {
                try {
                    $userProvider->resetUsageIfNeeded();

                    $provider = match($userProvider->provider) {
                        'openai' => new OpenAIProvider($userProvider->api_key),
                        'replicate' => new ReplicateProvider($userProvider->api_key),
                        'together' => new TogetherProvider($userProvider->api_key),
                        'anthropic' => new OpenAIProvider($userProvider->api_key), // Anthropic uses same interface
                        'google' => new OpenAIProvider($userProvider->api_key), // Google uses same interface
                        default => null,
                    };

                    if ($provider) {
                        $this->providers[$userProvider->provider] = $provider;
                    }
                } catch (\Exception $e) {
                    Log::warning("User provider {$userProvider->provider} not available: " . $e->getMessage());
                }
            }
        }

        // Fallback to global config if no user providers or user not authenticated
        if (empty($this->providers)) {
            try {
                $this->providers['openai'] = new OpenAIProvider();
            } catch (\Exception $e) {
                Log::warning('OpenAI provider not available: ' . $e->getMessage());
            }

            try {
                $this->providers['replicate'] = new ReplicateProvider();
            } catch (\Exception $e) {
                Log::warning('Replicate provider not available: ' . $e->getMessage());
            }

            try {
                $this->providers['together'] = new TogetherProvider();
            } catch (\Exception $e) {
                Log::warning('Together provider not available: ' . $e->getMessage());
            }
        }
    }

    /**
     * Set default provider
     */
    private function setDefaultProvider(): void
    {
        $defaultProvider = config('ai.default', 'openai');

        if (isset($this->providers[$defaultProvider])) {
            $this->currentProvider = $this->providers[$defaultProvider];

            // Set user provider if user is authenticated
            if ($this->user) {
                $this->userProvider = $this->user->getActiveAiProvider($defaultProvider);
            }
        } else {
            // Fallback to first available provider
            $available = $this->getAvailableProviders();
            if (!empty($available)) {
                $this->currentProvider = reset($available);

                // Try to set user provider for first available
                if ($this->user) {
                    $providerName = array_search($this->currentProvider, $this->providers);
                    if ($providerName) {
                        $this->userProvider = $this->user->getActiveAiProvider($providerName);
                    }
                }
            }
        }
    }

    /**
     * Get provider instance
     */
    private function getProvider(?string $name = null): AIProviderInterface
    {
        if ($name) {
            return $this->providers[$name] ?? throw new \InvalidArgumentException("Provider {$name} not found");
        }

        if (!$this->currentProvider) {
            throw new \RuntimeException('No AI provider available');
        }

        return $this->currentProvider;
    }

    /**
     * Track token usage
     */
    private function trackUsage(AIResponse $response, string $type, string $input): void
    {
        if (!config('ai.track_usage', true)) {
            return;
        }

        try {
            // Track in global token usage table
            TokenUsage::create([
                'user_id' => $this->user?->id ?? auth()->id(),
                'provider' => $response->provider,
                'model' => $response->metadata['model'] ?? 'unknown',
                'type' => $type,
                'prompt_tokens' => $response->promptTokens,
                'completion_tokens' => $response->completionTokens,
                'total_tokens' => $response->totalTokens,
                'cost' => $response->cost,
                'input_preview' => substr($input, 0, 255),
                'output_preview' => substr($response->content, 0, 255),
            ]);

            // Track in user provider if applicable
            if ($this->userProvider) {
                $this->userProvider->trackUsage(
                    $response->totalTokens,
                    $response->cost
                );
            }
        } catch (\Exception $e) {
            Log::error('Failed to track token usage', ['error' => $e->getMessage()]);
        }
    }
}
