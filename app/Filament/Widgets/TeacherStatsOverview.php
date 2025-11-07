<?php

namespace App\Filament\Widgets;

use App\Models\Exercise;
use App\Models\ExerciseAttempt;
use App\Models\Material;
use App\Models\TokenUsage;
use Filament\Widgets\StatsOverviewWidget as BaseWidget;
use Filament\Widgets\StatsOverviewWidget\Stat;

class TeacherStatsOverview extends BaseWidget
{
    protected static ?int $sort = 1;

    protected function getStats(): array
    {
        // Total exercises created
        $totalExercises = Exercise::where('user_id', auth()->id())->count();

        // Total materials uploaded
        $totalMaterials = Material::where('user_id', auth()->id())->count();

        // Materials processed with AI/OCR
        $processedMaterials = Material::where('user_id', auth()->id())
            ->where('is_processed', true)
            ->count();

        // Total student attempts on user's exercises
        $totalAttempts = ExerciseAttempt::whereHas('exercise', function ($query) {
            $query->where('user_id', auth()->id());
        })->count();

        // Average accuracy on user's exercises
        $avgAccuracy = ExerciseAttempt::whereHas('exercise', function ($query) {
            $query->where('user_id', auth()->id());
        })->avg('accuracy_percentage') ?? 0;

        // AI cost this month
        $aiCostThisMonth = TokenUsage::where('user_id', auth()->id())
            ->whereMonth('created_at', now()->month)
            ->sum('cost');

        // Last month's cost
        $aiCostLastMonth = TokenUsage::where('user_id', auth()->id())
            ->whereMonth('created_at', now()->subMonth()->month)
            ->sum('cost');

        $costChange = $aiCostLastMonth > 0
            ? round((($aiCostThisMonth - $aiCostLastMonth) / $aiCostLastMonth) * 100, 1)
            : 0;

        return [
            Stat::make('Active Exercises', number_format($totalExercises))
                ->description('Created by you')
                ->descriptionIcon('heroicon-o-academic-cap')
                ->color('primary')
                ->chart($this->getExercisesChart()),

            Stat::make('Student Engagement', number_format($totalAttempts) . ' attempts')
                ->description('Average accuracy: ' . round($avgAccuracy, 1) . '%')
                ->descriptionIcon('heroicon-o-users')
                ->color($avgAccuracy >= 70 ? 'success' : 'warning'),

            Stat::make('Materials', $processedMaterials . '/' . $totalMaterials . ' processed')
                ->description('AI/OCR processing status')
                ->descriptionIcon('heroicon-o-document-text')
                ->color('info')
                ->chart($this->getMaterialsChart()),

            Stat::make('AI Cost This Month', '$' . number_format($aiCostThisMonth, 2))
                ->description($costChange >= 0 ? "↑ {$costChange}% from last month" : "↓ " . abs($costChange) . "% from last month")
                ->descriptionIcon('heroicon-o-currency-dollar')
                ->color($costChange >= 0 ? 'warning' : 'success')
                ->chart($this->getCostChart()),
        ];
    }

    protected function getExercisesChart(): array
    {
        // Last 7 days of exercise creation
        $data = [];
        for ($i = 6; $i >= 0; $i--) {
            $date = now()->subDays($i)->startOfDay();
            $count = Exercise::where('user_id', auth()->id())
                ->whereDate('created_at', $date)
                ->count();
            $data[] = $count;
        }
        return $data;
    }

    protected function getMaterialsChart(): array
    {
        // Last 7 days of material uploads
        $data = [];
        for ($i = 6; $i >= 0; $i--) {
            $date = now()->subDays($i)->startOfDay();
            $count = Material::where('user_id', auth()->id())
                ->whereDate('created_at', $date)
                ->count();
            $data[] = $count;
        }
        return $data;
    }

    protected function getCostChart(): array
    {
        // Last 7 days of AI costs
        $data = [];
        for ($i = 6; $i >= 0; $i--) {
            $date = now()->subDays($i)->startOfDay();
            $cost = TokenUsage::where('user_id', auth()->id())
                ->whereDate('created_at', $date)
                ->sum('cost');
            $data[] = round($cost, 2);
        }
        return $data;
    }

    public static function canView(): bool
    {
        return auth()->user()->hasRole('profesor') || auth()->user()->hasRole('admin');
    }
}
