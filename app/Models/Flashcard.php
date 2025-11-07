<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\SoftDeletes;

class Flashcard extends Model
{
    use HasFactory, SoftDeletes;

    protected $fillable = [
        'user_id', 'subject_id', 'topic_id', 'material_id',
        'front', 'back', 'hint', 'notes',
        'easiness_factor', 'interval', 'repetitions',
        'next_review_at', 'last_reviewed_at',
        'total_reviews', 'correct_reviews', 'streak',
        'is_active'
    ];

    protected $casts = [
        'next_review_at' => 'datetime',
        'last_reviewed_at' => 'datetime',
        'is_active' => 'boolean',
    ];

    // Relationships
    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    public function subject(): BelongsTo
    {
        return $this->belongsTo(Subject::class);
    }

    public function topic(): BelongsTo
    {
        return $this->belongsTo(Topic::class);
    }

    public function material(): BelongsTo
    {
        return $this->belongsTo(Material::class);
    }

    public function reviews(): HasMany
    {
        return $this->hasMany(FlashcardReview::class);
    }

    // SM-2 Algorithm Implementation
    /**
     * Record a review and update SRS parameters
     *
     * @param int $rating 0-5 quality rating
     * @param int $timeTaken seconds taken to answer
     * @return FlashcardReview
     */
    public function review(int $rating, int $timeTaken = null): FlashcardReview
    {
        // Store current state
        $intervalBefore = $this->interval;
        $easinessFactorBefore = $this->easiness_factor;

        // Calculate new values using SM-2
        $this->calculateNextReview($rating);

        // Update statistics
        $this->total_reviews++;
        if ($rating >= 3) {
            $this->correct_reviews++;
            $this->streak++;
        } else {
            $this->streak = 0;
        }

        $this->last_reviewed_at = now();
        $this->save();

        // Create review record
        return $this->reviews()->create([
            'user_id' => $this->user_id,
            'rating' => $rating,
            'time_taken' => $timeTaken,
            'interval_before' => $intervalBefore,
            'interval_after' => $this->interval,
            'easiness_factor_before' => $easinessFactorBefore,
            'easiness_factor_after' => $this->easiness_factor,
        ]);
    }

    /**
     * Calculate next review date using SM-2 algorithm
     *
     * @param int $rating Quality of response (0-5)
     */
    protected function calculateNextReview(int $rating): void
    {
        // Convert easiness_factor from stored format (250 = 2.5)
        $ef = $this->easiness_factor / 100;

        // SM-2 formula: EF' = EF + (0.1 - (5 - q) * (0.08 + (5 - q) * 0.02))
        $ef = $ef + (0.1 - (5 - $rating) * (0.08 + (5 - $rating) * 0.02));

        // Easiness factor should be at least 1.3
        if ($ef < 1.3) {
            $ef = 1.3;
        }

        // Calculate interval
        if ($rating < 3) {
            // If quality < 3, start over
            $this->interval = 0;
            $this->repetitions = 0;
        } else {
            // Successful review
            if ($this->repetitions == 0) {
                $this->interval = 1;
            } elseif ($this->repetitions == 1) {
                $this->interval = 6;
            } else {
                $this->interval = (int) ceil($this->interval * $ef);
            }
            $this->repetitions++;
        }

        // Store easiness factor (2.5 -> 250)
        $this->easiness_factor = (int) round($ef * 100);

        // Calculate next review date
        $this->next_review_at = now()->addDays($this->interval);
    }

    /**
     * Get flashcards due for review
     */
    public static function dueForReview(int $userId, int $limit = 20)
    {
        return static::where('user_id', $userId)
            ->where('is_active', true)
            ->where(function ($query) {
                $query->whereNull('next_review_at')
                    ->orWhere('next_review_at', '<=', now());
            })
            ->orderBy('next_review_at')
            ->limit($limit)
            ->get();
    }

    /**
     * Get accuracy percentage
     */
    public function getAccuracyAttribute(): float
    {
        if ($this->total_reviews == 0) {
            return 0;
        }
        return round(($this->correct_reviews / $this->total_reviews) * 100, 1);
    }

    /**
     * Get easiness factor as decimal
     */
    public function getEasinessAttribute(): float
    {
        return $this->easiness_factor / 100;
    }

    /**
     * Reset card to beginning
     */
    public function reset(): void
    {
        $this->update([
            'easiness_factor' => 250,
            'interval' => 0,
            'repetitions' => 0,
            'next_review_at' => now(),
            'streak' => 0,
        ]);
    }
}
