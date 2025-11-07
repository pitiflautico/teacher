<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::create('flashcard_reviews', function (Blueprint $table) {
            $table->id();
            $table->foreignId('flashcard_id')->constrained()->cascadeOnDelete();
            $table->foreignId('user_id')->constrained()->cascadeOnDelete();

            // Rating: 0-5 (SM-2 algorithm)
            // 0: Complete blackout
            // 1: Incorrect, but familiar
            // 2: Incorrect, but easy to remember
            // 3: Correct, but difficult
            // 4: Correct with hesitation
            // 5: Perfect recall
            $table->integer('rating');

            // Time taken to answer (seconds)
            $table->integer('time_taken')->nullable();

            // SRS state at time of review
            $table->integer('interval_before')->nullable();
            $table->integer('interval_after')->nullable();
            $table->integer('easiness_factor_before')->nullable();
            $table->integer('easiness_factor_after')->nullable();

            $table->timestamps();

            // Indexes
            $table->index(['flashcard_id', 'created_at']);
            $table->index(['user_id', 'created_at']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('flashcard_reviews');
    }
};
