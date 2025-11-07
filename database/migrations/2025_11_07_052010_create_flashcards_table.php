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
        Schema::create('flashcards', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->constrained()->cascadeOnDelete();
            $table->foreignId('subject_id')->nullable()->constrained()->nullOnDelete();
            $table->foreignId('topic_id')->nullable()->constrained()->nullOnDelete();
            $table->foreignId('material_id')->nullable()->constrained()->nullOnDelete();

            $table->string('front'); // Question or term
            $table->text('back'); // Answer or definition
            $table->text('hint')->nullable();
            $table->text('notes')->nullable();

            // Spaced Repetition System (SRS) data
            $table->integer('easiness_factor')->default(250); // SM-2 algorithm (2.5 * 100)
            $table->integer('interval')->default(0); // Days until next review
            $table->integer('repetitions')->default(0); // Number of successful reviews
            $table->timestamp('next_review_at')->nullable(); // When to review next
            $table->timestamp('last_reviewed_at')->nullable();

            // Statistics
            $table->integer('total_reviews')->default(0);
            $table->integer('correct_reviews')->default(0);
            $table->integer('streak')->default(0); // Current correct streak

            $table->boolean('is_active')->default(true);
            $table->timestamps();
            $table->softDeletes();

            // Indexes
            $table->index(['user_id', 'next_review_at']);
            $table->index(['user_id', 'subject_id']);
            $table->index('is_active');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('flashcards');
    }
};
