<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class TokenUsage extends Model
{
    use HasFactory;
    protected $fillable = [
        'user_id',
        'provider',
        'model',
        'type',
        'prompt_tokens',
        'completion_tokens',
        'total_tokens',
        'cost',
        'input_preview',
        'output_preview',
        'metadata',
    ];

    protected $casts = [
        'prompt_tokens' => 'integer',
        'completion_tokens' => 'integer',
        'total_tokens' => 'integer',
        'cost' => 'decimal:6',
        'metadata' => 'array',
    ];

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    /**
     * Get total cost for a period
     */
    public static function getTotalCost(\DateTime $start, \DateTime $end): float
    {
        return static::whereBetween('created_at', [$start, $end])
            ->sum('cost');
    }

    /**
     * Get usage by provider
     */
    public static function getUsageByProvider(\DateTime $start, \DateTime $end): array
    {
        return static::whereBetween('created_at', [$start, $end])
            ->selectRaw('provider, SUM(total_tokens) as tokens, SUM(cost) as cost')
            ->groupBy('provider')
            ->get()
            ->toArray();
    }
}
