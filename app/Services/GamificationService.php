<?php

namespace App\Services;

use App\Models\Badge;
use App\Models\Point;
use App\Models\User;
use App\Models\ExerciseAttempt;
use App\Models\FlashcardReview;
use Illuminate\Support\Facades\DB;

class GamificationService
{
    /**
     * Award points for completing an exercise
     */
    public function awardExerciseCompletion(User $user, ExerciseAttempt $attempt): void
    {
        $points = $attempt->is_correct ? 50 : 10;

        Point::award($user, $attempt->exercise, $points,
            $attempt->is_correct ? 'Correct exercise answer' : 'Exercise attempt'
        );

        $this->checkExerciseBadges($user);
        $this->checkPointsBadges($user);
    }

    /**
     * Award points for reviewing a flashcard
     */
    public function awardFlashcardReview(User $user, FlashcardReview $review): void
    {
        $points = match ($review->quality) {
            5 => 20,
            4 => 15,
            3 => 10,
            default => 5
        };

        Point::award($user, $review->flashcard, $points, 'Flashcard review');

        $this->checkFlashcardBadges($user);
        $this->checkPointsBadges($user);
    }

    /**
     * Award points for studying material
     */
    public function awardMaterialStudy(User $user, $material): void
    {
        Point::award($user, $material, 30, 'Material studied');
        $this->checkMaterialBadges($user);
        $this->checkPointsBadges($user);
    }

    /**
     * Award points for creating a mind map
     */
    public function awardMindMapCreation(User $user, $mindMap): void
    {
        Point::award($user, $mindMap, 75, 'Mind map created');
        $this->checkMindMapBadges($user);
        $this->checkPointsBadges($user);
    }

    /**
     * Award points for joining a group
     */
    public function awardGroupJoin(User $user, $group): void
    {
        Point::award($user, $group, 25, 'Joined study group');
        $this->checkGroupBadges($user);
        $this->checkPointsBadges($user);
    }

    /**
     * Check and award exercise badges
     */
    protected function checkExerciseBadges(User $user): void
    {
        $count = ExerciseAttempt::where('user_id', $user->id)
            ->where('is_correct', true)
            ->distinct('exercise_id')
            ->count('exercise_id');

        $this->checkBadgeRequirement($user, 'exercises_completed', $count);
    }

    /**
     * Check and award flashcard badges
     */
    protected function checkFlashcardBadges(User $user): void
    {
        $count = FlashcardReview::where('user_id', $user->id)->count();
        $this->checkBadgeRequirement($user, 'flashcards_reviewed', $count);
    }

    /**
     * Check and award material badges
     */
    protected function checkMaterialBadges(User $user): void
    {
        $count = Point::where('user_id', $user->id)
            ->where('pointable_type', 'App\\Models\\Material')
            ->distinct('pointable_id')
            ->count('pointable_id');

        $this->checkBadgeRequirement($user, 'materials_studied', $count);
    }

    /**
     * Check and award mind map badges
     */
    protected function checkMindMapBadges(User $user): void
    {
        $count = $user->mindMaps()->count();
        $this->checkBadgeRequirement($user, 'mind_maps_created', $count);
    }

    /**
     * Check and award group badges
     */
    protected function checkGroupBadges(User $user): void
    {
        $count = $user->groups()->count();
        $this->checkBadgeRequirement($user, 'groups_joined', $count);
    }

    /**
     * Check and award points badges
     */
    protected function checkPointsBadges(User $user): void
    {
        $totalPoints = $user->totalPoints();
        $this->checkBadgeRequirement($user, 'points_earned', $totalPoints);
    }

    /**
     * Check if user meets badge requirement and award it
     */
    protected function checkBadgeRequirement(User $user, string $requirementType, int $currentValue): void
    {
        $badges = Badge::where('requirement_type', $requirementType)
            ->where('requirement_value', '<=', $currentValue)
            ->whereNotIn('id', function($query) use ($user) {
                $query->select('badge_id')
                    ->from('user_badges')
                    ->where('user_id', $user->id);
            })
            ->get();

        foreach ($badges as $badge) {
            $badge->awardTo($user);
        }
    }
}
