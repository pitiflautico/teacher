<?php

namespace App\Filament\Widgets;

use App\Models\Badge;
use App\Models\ExerciseAttempt;
use App\Models\FlashcardReview;
use App\Models\Point;
use App\Models\User;
use Filament\Widgets\StatsOverviewWidget as BaseWidget;
use Filament\Widgets\StatsOverviewWidget\Stat;

class GamificationStatsWidget extends BaseWidget
{
    protected function getStats(): array
    {
        $totalUsers = User::count();
        $totalPoints = Point::sum('points');
        $totalBadgesUnlocked = \DB::table('user_badges')->count();
        $avgPointsPerUser = $totalUsers > 0 ? round($totalPoints / $totalUsers) : 0;

        return [
            Stat::make('Total Users', $totalUsers)
                ->description('Active students')
                ->descriptionIcon('heroicon-m-user-group')
                ->color('success'),

            Stat::make('Total Points Awarded', number_format($totalPoints))
                ->description($avgPointsPerUser . ' average per user')
                ->descriptionIcon('heroicon-m-star')
                ->color('warning'),

            Stat::make('Badges Unlocked', $totalBadgesUnlocked)
                ->description('Out of ' . (Badge::count() * $totalUsers) . ' possible')
                ->descriptionIcon('heroicon-m-trophy')
                ->color('info'),

            Stat::make('Exercise Completions', ExerciseAttempt::where('is_correct', true)->count())
                ->description('Correct answers only')
                ->descriptionIcon('heroicon-m-check-circle')
                ->color('success'),

            Stat::make('Flashcard Reviews', FlashcardReview::count())
                ->description('Total study sessions')
                ->descriptionIcon('heroicon-m-academic-cap')
                ->color('primary'),
        ];
    }
}
