<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class SavedResource extends Model
{
    protected $fillable = [
        'user_id',
        'subject_id',
        'title',
        'url',
        'snippet',
        'type',
        'relevance',
        'ai_reason',
        'source',
        'notes',
        'is_favorite',
        'accessed_at',
    ];

    protected $casts = [
        'is_favorite' => 'boolean',
        'relevance' => 'integer',
        'accessed_at' => 'datetime',
    ];

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    public function subject(): BelongsTo
    {
        return $this->belongsTo(Subject::class);
    }

    public function markAsAccessed(): void
    {
        $this->update(['accessed_at' => now()]);
    }

    public function toggleFavorite(): void
    {
        $this->update(['is_favorite' => !$this->is_favorite]);
    }
}
