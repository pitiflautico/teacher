<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class ExerciseAttempt extends Model
{
    use HasFactory;
    protected $fillable = [
        'exercise_id', 'user_id', 'user_answers', 'is_correct',
        'score', 'max_score', 'accuracy_percentage', 'time_taken',
        'ai_feedback', 'started_at', 'completed_at'
    ];

    protected $casts = [
        'user_answers' => 'array',
        'is_correct' => 'boolean',
        'score' => 'integer',
        'max_score' => 'integer',
        'accuracy_percentage' => 'decimal:2',
        'time_taken' => 'integer',
        'started_at' => 'datetime',
        'completed_at' => 'datetime',
    ];

    public function exercise(): BelongsTo
    {
        return $this->belongsTo(Exercise::class);
    }

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    public function calculateScore(): void
    {
        $exercise = $this->exercise;
        $this->is_correct = $exercise->checkAnswer($this->user_answers);
        $this->max_score = $exercise->points;
        $this->score = $this->is_correct ? $exercise->points : 0;
        $this->accuracy_percentage = $this->is_correct ? 100 : 0;
        $this->save();
    }
}
