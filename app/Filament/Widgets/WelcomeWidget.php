<?php

namespace App\Filament\Widgets;

use Filament\Widgets\Widget;

class WelcomeWidget extends Widget
{
    protected static string $view = 'filament.widgets.welcome-widget';

    protected int | string | array $columnSpan = 'full';

    protected static ?int $sort = -10;

    public function getData(): array
    {
        $user = auth()->user();

        return [
            'name' => $user->name,
            'email' => $user->email,
            'total_points' => $user->totalPoints() ?? 0,
            'level' => $user->level() ?? 1,
            'badges_count' => $user->badges()->count() ?? 0,
            'subjects_count' => $user->subjects()->count() ?? 0,
            'exercises_completed' => $user->exerciseAttempts()->where('is_correct', true)->count() ?? 0,
            'study_streak' => $this->calculateStudyStreak(),
        ];
    }

    protected function calculateStudyStreak(): int
    {
        $user = auth()->user();
        $streak = 0;
        $currentDate = now();

        while (true) {
            $hasActivity = $user->exerciseAttempts()
                ->whereDate('created_at', $currentDate)
                ->exists()
                || $user->flashcardReviews()
                ->whereDate('created_at', $currentDate)
                ->exists();

            if (!$hasActivity) {
                break;
            }

            $streak++;
            $currentDate = $currentDate->subDay();
        }

        return $streak;
    }
}
