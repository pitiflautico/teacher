<?php

namespace App\Filament\Widgets;

use App\Models\ExerciseAttempt;
use Filament\Widgets\StatsOverviewWidget as BaseWidget;
use Filament\Widgets\StatsOverviewWidget\Stat;

class StudentStatsOverview extends BaseWidget
{
    protected static ?int $sort = 1;

    protected function getStats(): array
    {
        $userId = auth()->id();

        // Total exercises completed
        $totalAttempts = ExerciseAttempt::where('user_id', $userId)->count();

        // Correct answers
        $correctAnswers = ExerciseAttempt::where('user_id', $userId)
            ->where('is_correct', true)
            ->count();

        // Total points earned
        $totalPoints = ExerciseAttempt::where('user_id', $userId)
            ->sum('score');

        // Accuracy percentage
        $accuracy = $totalAttempts > 0
            ? round(($correctAnswers / $totalAttempts) * 100, 1)
            : 0;

        // This week's attempts
        $thisWeekAttempts = ExerciseAttempt::where('user_id', $userId)
            ->whereBetween('created_at', [now()->startOfWeek(), now()->endOfWeek()])
            ->count();

        // Last week's attempts
        $lastWeekAttempts = ExerciseAttempt::where('user_id', $userId)
            ->whereBetween('created_at', [
                now()->subWeek()->startOfWeek(),
                now()->subWeek()->endOfWeek()
            ])
            ->count();

        $weeklyChange = $lastWeekAttempts > 0
            ? round((($thisWeekAttempts - $lastWeekAttempts) / $lastWeekAttempts) * 100, 1)
            : 0;

        return [
            Stat::make('Total Points', number_format($totalPoints))
                ->description('Earned from correct answers')
                ->descriptionIcon('heroicon-o-trophy')
                ->color('success')
                ->chart($this->getPointsChart()),

            Stat::make('Accuracy Rate', $accuracy . '%')
                ->description($correctAnswers . ' correct out of ' . $totalAttempts . ' attempts')
                ->descriptionIcon($accuracy >= 70 ? 'heroicon-o-check-circle' : 'heroicon-o-x-circle')
                ->color($accuracy >= 70 ? 'success' : ($accuracy >= 50 ? 'warning' : 'danger')),

            Stat::make('Weekly Activity', $thisWeekAttempts . ' exercises')
                ->description($weeklyChange >= 0 ? "↑ {$weeklyChange}% from last week" : "↓ " . abs($weeklyChange) . "% from last week")
                ->descriptionIcon($weeklyChange >= 0 ? 'heroicon-o-arrow-trending-up' : 'heroicon-o-arrow-trending-down')
                ->color($weeklyChange >= 0 ? 'success' : 'danger')
                ->chart($this->getWeeklyChart()),

            Stat::make('Exercises Completed', number_format($totalAttempts))
                ->description('Total exercises attempted')
                ->descriptionIcon('heroicon-o-academic-cap')
                ->color('info'),
        ];
    }

    protected function getPointsChart(): array
    {
        // Get last 7 days of points
        $data = [];
        for ($i = 6; $i >= 0; $i--) {
            $date = now()->subDays($i)->startOfDay();
            $points = ExerciseAttempt::where('user_id', auth()->id())
                ->whereDate('created_at', $date)
                ->sum('score');
            $data[] = $points;
        }
        return $data;
    }

    protected function getWeeklyChart(): array
    {
        // Get last 7 days of attempts
        $data = [];
        for ($i = 6; $i >= 0; $i--) {
            $date = now()->subDays($i)->startOfDay();
            $count = ExerciseAttempt::where('user_id', auth()->id())
                ->whereDate('created_at', $date)
                ->count();
            $data[] = $count;
        }
        return $data;
    }

    public static function canView(): bool
    {
        return auth()->user()->hasRole('estudiante');
    }
}
