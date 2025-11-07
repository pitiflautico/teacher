<?php

namespace App\Providers;

use Illuminate\Support\ServiceProvider;

class AppServiceProvider extends ServiceProvider
{
    /**
     * Register any application services.
     */
    public function register(): void
    {
        //
    }

    /**
     * Bootstrap any application services.
     */
    public function boot(): void
    {
        // Register observers for gamification
        \App\Models\ExerciseAttempt::observe(\App\Observers\ExerciseAttemptObserver::class);
        \App\Models\FlashcardReview::observe(\App\Observers\FlashcardReviewObserver::class);
    }
}
