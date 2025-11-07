<?php

namespace App\Observers;

use App\Models\FlashcardReview;
use App\Services\GamificationService;

class FlashcardReviewObserver
{
    protected $gamificationService;

    public function __construct(GamificationService $gamificationService)
    {
        $this->gamificationService = $gamificationService;
    }

    /**
     * Handle the FlashcardReview "created" event.
     */
    public function created(FlashcardReview $flashcardReview): void
    {
        if ($flashcardReview->user) {
            $this->gamificationService->awardFlashcardReview(
                $flashcardReview->user,
                $flashcardReview
            );
        }
    }

    /**
     * Handle the FlashcardReview "updated" event.
     */
    public function updated(FlashcardReview $flashcardReview): void
    {
        //
    }

    /**
     * Handle the FlashcardReview "deleted" event.
     */
    public function deleted(FlashcardReview $flashcardReview): void
    {
        //
    }

    /**
     * Handle the FlashcardReview "restored" event.
     */
    public function restored(FlashcardReview $flashcardReview): void
    {
        //
    }

    /**
     * Handle the FlashcardReview "force deleted" event.
     */
    public function forceDeleted(FlashcardReview $flashcardReview): void
    {
        //
    }
}
