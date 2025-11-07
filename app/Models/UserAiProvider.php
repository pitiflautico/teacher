<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class UserAiProvider extends Model
{
    protected $fillable = [
        'user_id',
        'provider',
        'api_key',
        'is_active',
        'token_limit',
        'cost_limit',
        'tokens_used',
        'cost_spent',
        'usage_reset_at',
    ];

    protected $casts = [
        'api_key' => 'encrypted',
        'is_active' => 'boolean',
        'tokens_used' => 'integer',
        'cost_spent' => 'decimal:2',
        'token_limit' => 'integer',
        'cost_limit' => 'decimal:2',
        'usage_reset_at' => 'datetime',
    ];

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    public function services(): HasMany
    {
        return $this->hasMany(ProviderService::class);
    }

    /**
     * Check if the provider has reached its token limit
     */
    public function hasReachedTokenLimit(): bool
    {
        if ($this->token_limit === null) {
            return false;
        }

        return $this->tokens_used >= $this->token_limit;
    }

    /**
     * Check if the provider has reached its cost limit
     */
    public function hasReachedCostLimit(): bool
    {
        if ($this->cost_limit === null) {
            return false;
        }

        return $this->cost_spent >= $this->cost_limit;
    }

    /**
     * Check if the provider can be used
     */
    public function canUse(): bool
    {
        return $this->is_active
            && !$this->hasReachedTokenLimit()
            && !$this->hasReachedCostLimit();
    }

    /**
     * Track usage for this provider
     */
    public function trackUsage(int $tokens, float $cost): void
    {
        $this->increment('tokens_used', $tokens);
        $this->increment('cost_spent', $cost);
    }

    /**
     * Reset monthly usage if needed
     */
    public function resetUsageIfNeeded(): void
    {
        if ($this->usage_reset_at === null || $this->usage_reset_at->isPast()) {
            $this->update([
                'tokens_used' => 0,
                'cost_spent' => 0,
                'usage_reset_at' => now()->addMonth(),
            ]);
        }
    }

    /**
     * Get provider label
     */
    public function getProviderLabelAttribute(): string
    {
        return match($this->provider) {
            'openai' => 'OpenAI (GPT)',
            'anthropic' => 'Anthropic (Claude)',
            'google' => 'Google (Gemini)',
            'replicate' => 'Replicate (Llama)',
            'together' => 'Together AI (Llama 3.1)',
            default => ucfirst($this->provider),
        };
    }
}
