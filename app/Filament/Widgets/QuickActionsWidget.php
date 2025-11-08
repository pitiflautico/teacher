<?php

namespace App\Filament\Widgets;

use Filament\Widgets\Widget;

class QuickActionsWidget extends Widget
{
    protected static string $view = 'filament.widgets.quick-actions-widget';

    protected int | string | array $columnSpan = 'full';

    protected static ?int $sort = -5;

    public function getActions(): array
    {
        return [
            [
                'label' => __('Upload Homework'),
                'description' => __('Upload your notes or homework documents'),
                'icon' => 'heroicon-o-cloud-arrow-up',
                'color' => 'primary',
                'url' => route('filament.admin.pages.upload-homework'),
            ],
            [
                'label' => __('Practice Exercises'),
                'description' => __('Answer questions to test your knowledge'),
                'icon' => 'heroicon-o-academic-cap',
                'color' => 'success',
                'url' => route('filament.admin.resources.exercises.index'),
            ],
            [
                'label' => __('Study Flashcards'),
                'description' => __('Review flashcards with spaced repetition'),
                'icon' => 'heroicon-o-sparkles',
                'color' => 'warning',
                'url' => route('filament.admin.resources.flashcards.index'),
            ],
            [
                'label' => __('View Progress'),
                'description' => __('Check your points, badges, and level'),
                'icon' => 'heroicon-o-chart-bar',
                'color' => 'info',
                'url' => route('filament.admin.pages.dashboard'),
            ],
        ];
    }
}
