<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\SoftDeletes;

class Exercise extends Model
{
    use HasFactory, SoftDeletes;

    protected $fillable = [
        'user_id', 'subject_id', 'topic_id', 'material_id',
        'title', 'description', 'type', 'difficulty', 'question',
        'options', 'correct_answers', 'explanation', 'hints',
        'contains_math', 'ai_metadata', 'points', 'time_limit', 'is_active'
    ];

    protected $casts = [
        'options' => 'array',
        'correct_answers' => 'array',
        'ai_metadata' => 'array',
        'contains_math' => 'boolean',
        'is_active' => 'boolean',
        'points' => 'integer',
        'time_limit' => 'integer',
    ];

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

    public function attempts(): HasMany
    {
        return $this->hasMany(ExerciseAttempt::class);
    }

    public function checkAnswer(array $userAnswers): bool
    {
        return match($this->type) {
            'multiple_choice' => $this->checkMultipleChoice($userAnswers),
            'true_false' => $this->checkTrueFalse($userAnswers),
            'short_answer' => $this->checkShortAnswer($userAnswers),
            default => false,
        };
    }

    private function checkMultipleChoice(array $userAnswers): bool
    {
        sort($userAnswers);
        $correct = $this->correct_answers;
        sort($correct);
        return $userAnswers === $correct;
    }

    private function checkTrueFalse(array $userAnswers): bool
    {
        return isset($userAnswers[0]) && $userAnswers[0] === $this->correct_answers[0];
    }

    private function checkShortAnswer(array $userAnswers): bool
    {
        if (!isset($userAnswers[0])) return false;

        $userAnswer = strtolower(trim($userAnswers[0]));
        foreach ($this->correct_answers as $correct) {
            if (strtolower(trim($correct)) === $userAnswer) {
                return true;
            }
        }
        return false;
    }
}
