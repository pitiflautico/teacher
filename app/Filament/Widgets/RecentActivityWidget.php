<?php

namespace App\Filament\Widgets;

use App\Models\ExerciseAttempt;
use App\Models\FlashcardReview;
use App\Models\UserBadge;
use Filament\Widgets\Widget;
use Illuminate\Support\Collection;

class RecentActivityWidget extends Widget
{
    protected static string $view = 'filament.widgets.recent-activity-widget';

    protected int | string | array $columnSpan = 2;

    protected static ?int $sort = 2;

    public function getActivities(): Collection
    {
        $user = auth()->user();
        $activities = collect();

        // Get recent exercise attempts
        $exercises = ExerciseAttempt::with(['exercise'])
            ->where('user_id', $user->id)
            ->latest()
            ->limit(3)
            ->get()
            ->map(function ($attempt) {
                return [
                    'type' => 'exercise',
                    'title' => 'Exercise Completed',
                    'description' => $attempt->exercise->title ?? 'Exercise',
                    'time' => $attempt->created_at->diffForHumans(),
                    'icon' => 'heroicon-s-academic-cap',
                    'color' => $attempt->is_correct ? 'success' : 'warning',
                    'status' => $attempt->is_correct ? 'Correct' : 'Practice',
                ];
            });

        // Get recent flashcard reviews
        $flashcards = FlashcardReview::with(['flashcard'])
            ->where('user_id', $user->id)
            ->latest()
            ->limit(2)
            ->get()
            ->map(function ($review) {
                return [
                    'type' => 'flashcard',
                    'title' => 'Flashcard Reviewed',
                    'description' => $review->flashcard->front ?? 'Flashcard',
                    'time' => $review->created_at->diffForHumans(),
                    'icon' => 'heroicon-s-sparkles',
                    'color' => 'info',
                    'status' => ucfirst($review->quality),
                ];
            });

        // Get recent badges
        $badges = UserBadge::with(['badge'])
            ->where('user_id', $user->id)
            ->latest()
            ->limit(2)
            ->get()
            ->map(function ($userBadge) {
                return [
                    'type' => 'badge',
                    'title' => 'Badge Unlocked',
                    'description' => $userBadge->badge->name ?? 'New Badge',
                    'time' => $userBadge->created_at->diffForHumans(),
                    'icon' => 'heroicon-s-trophy',
                    'color' => 'warning',
                    'status' => 'Completed',
                ];
            });

        return $activities
            ->merge($exercises)
            ->merge($flashcards)
            ->merge($badges)
            ->sortByDesc('time')
            ->take(8);
    }
}
