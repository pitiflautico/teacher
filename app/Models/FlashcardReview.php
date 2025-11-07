<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class FlashcardReview extends Model
{
    use HasFactory;

    protected $fillable = [
        'flashcard_id', 'user_id', 'rating', 'time_taken',
        'interval_before', 'interval_after',
        'easiness_factor_before', 'easiness_factor_after'
    ];

    public function flashcard(): BelongsTo
    {
        return $this->belongsTo(Flashcard::class);
    }

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }
}
