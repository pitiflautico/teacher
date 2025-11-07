<?php

namespace App\Observers;

use App\Models\ExerciseAttempt;
use App\Services\GamificationService;

class ExerciseAttemptObserver
{
    protected $gamificationService;

    public function __construct(GamificationService $gamificationService)
    {
        $this->gamificationService = $gamificationService;
    }

    /**
     * Handle the ExerciseAttempt "created" event.
     */
    public function created(ExerciseAttempt $exerciseAttempt): void
    {
        if ($exerciseAttempt->user) {
            $this->gamificationService->awardExerciseCompletion(
                $exerciseAttempt->user,
                $exerciseAttempt
            );
        }
    }

    /**
     * Handle the ExerciseAttempt "updated" event.
     */
    public function updated(ExerciseAttempt $exerciseAttempt): void
    {
        //
    }

    /**
     * Handle the ExerciseAttempt "deleted" event.
     */
    public function deleted(ExerciseAttempt $exerciseAttempt): void
    {
        //
    }

    /**
     * Handle the ExerciseAttempt "restored" event.
     */
    public function restored(ExerciseAttempt $exerciseAttempt): void
    {
        //
    }

    /**
     * Handle the ExerciseAttempt "force deleted" event.
     */
    public function forceDeleted(ExerciseAttempt $exerciseAttempt): void
    {
        //
    }
}
