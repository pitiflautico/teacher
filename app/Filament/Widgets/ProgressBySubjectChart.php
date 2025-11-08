<?php

namespace App\Filament\Widgets;

use App\Models\ExerciseAttempt;
use Filament\Widgets\ChartWidget;
use Illuminate\Support\Facades\DB;

class ProgressBySubjectChart extends ChartWidget
{
    protected static ?string $heading = 'Progress by Subject';

    protected static ?int $sort = 2;

    protected static ?string $maxHeight = '300px';

    protected function getData(): array
    {
        $userId = auth()->id();

        // Get attempts grouped by subject
        $data = ExerciseAttempt::where('exercise_attempts.user_id', $userId)
            ->join('exercises', 'exercise_attempts.exercise_id', '=', 'exercises.id')
            ->join('subjects', 'exercises.subject_id', '=', 'subjects.id')
            ->select(
                'subjects.name as subject_name',
                DB::raw('COUNT(exercise_attempts.id) as total_attempts'),
                DB::raw('SUM(CASE WHEN exercise_attempts.is_correct THEN 1 ELSE 0 END) as correct_attempts')
            )
            ->groupBy('subjects.id', 'subjects.name')
            ->get();

        $labels = $data->pluck('subject_name')->toArray();
        $correctData = $data->map(fn ($item) => $item->correct_attempts)->toArray();
        $incorrectData = $data->map(fn ($item) => $item->total_attempts - $item->correct_attempts)->toArray();

        return [
            'datasets' => [
                [
                    'label' => 'Correct',
                    'data' => $correctData,
                    'backgroundColor' => '#10b981',
                ],
                [
                    'label' => 'Incorrect',
                    'data' => $incorrectData,
                    'backgroundColor' => '#ef4444',
                ],
            ],
            'labels' => $labels,
        ];
    }

    protected function getType(): string
    {
        return 'bar';
    }

    protected function getOptions(): array
    {
        return [
            'scales' => [
                'y' => [
                    'stacked' => true,
                    'beginAtZero' => true,
                ],
                'x' => [
                    'stacked' => true,
                ],
            ],
            'plugins' => [
                'legend' => [
                    'display' => true,
                ],
            ],
        ];
    }

    public static function canView(): bool
    {
        return auth()->user()->hasRole('estudiante');
    }
}
