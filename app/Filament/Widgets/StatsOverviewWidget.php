<?php

namespace App\Filament\Widgets;

use App\Models\Exercise;
use App\Models\ExerciseAttempt;
use App\Models\Flashcard;
use App\Models\Material;
use Filament\Widgets\StatsOverviewWidget as BaseWidget;
use Filament\Widgets\StatsOverviewWidget\Stat;

class StatsOverviewWidget extends BaseWidget
{
    protected static ?int $sort = 0;

    protected int | string | array $columnSpan = 'full';

    protected function getStats(): array
    {
        $user = auth()->user();

        // Calculate stats
        $totalMaterials = Material::where('user_id', $user->id)->count();
        $totalExercises = Exercise::where('user_id', $user->id)->count();
        $totalFlashcards = Flashcard::where('user_id', $user->id)->count();
        $exercisesCompleted = ExerciseAttempt::where('user_id', $user->id)
            ->where('is_correct', true)
            ->count();

        // Calculate completion rate
        $totalAttempts = ExerciseAttempt::where('user_id', $user->id)->count();
        $completionRate = $totalAttempts > 0 ? round(($exercisesCompleted / $totalAttempts) * 100, 1) : 0;

        // Calculate trend (compared to last 7 days)
        $recentCompleted = ExerciseAttempt::where('user_id', $user->id)
            ->where('is_correct', true)
            ->where('created_at', '>=', now()->subDays(7))
            ->count();

        $previousCompleted = ExerciseAttempt::where('user_id', $user->id)
            ->where('is_correct', true)
            ->whereBetween('created_at', [now()->subDays(14), now()->subDays(7)])
            ->count();

        $trend = $previousCompleted > 0 ? round((($recentCompleted - $previousCompleted) / $previousCompleted) * 100, 1) : 0;

        return [
            Stat::make('Total Materials', number_format($totalMaterials))
                ->description('Study materials uploaded')
                ->descriptionIcon('heroicon-m-document-text')
                ->color('primary')
                ->chart([7, 12, 16, 20, 22, 25, $totalMaterials]),

            Stat::make('Exercises Available', number_format($totalExercises))
                ->description('Ready to practice')
                ->descriptionIcon('heroicon-m-academic-cap')
                ->color('info')
                ->chart([10, 25, 35, 45, 60, 75, $totalExercises]),

            Stat::make('Flashcards', number_format($totalFlashcards))
                ->description('Active flashcards')
                ->descriptionIcon('heroicon-m-sparkles')
                ->color('warning')
                ->chart([5, 15, 25, 35, 45, 55, $totalFlashcards]),

            Stat::make('Completion Rate', $completionRate . '%')
                ->description($trend >= 0 ? "+{$trend}% from last week" : "{$trend}% from last week")
                ->descriptionIcon($trend >= 0 ? 'heroicon-m-arrow-trending-up' : 'heroicon-m-arrow-trending-down')
                ->color($trend >= 0 ? 'success' : 'danger')
                ->chart([45, 50, 55, 60, 65, 70, $completionRate]),
        ];
    }
}
