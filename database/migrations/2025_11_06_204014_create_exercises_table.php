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
        Schema::create('exercises', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->constrained()->onDelete('cascade');
            $table->foreignId('subject_id')->nullable()->constrained()->onDelete('set null');
            $table->foreignId('topic_id')->nullable()->constrained()->onDelete('set null');
            $table->foreignId('material_id')->nullable()->constrained()->onDelete('set null');
            $table->string('title');
            $table->text('description')->nullable();
            $table->enum('type', ['multiple_choice', 'true_false', 'short_answer', 'essay', 'problem_solving'])->default('multiple_choice');
            $table->enum('difficulty', ['easy', 'medium', 'hard'])->default('medium');
            $table->longText('question');
            $table->json('options')->nullable(); // For multiple choice
            $table->json('correct_answers'); // Can be multiple for some types
            $table->text('explanation')->nullable();
            $table->text('hints')->nullable();
            $table->boolean('contains_math')->default(false); // For KaTeX rendering
            $table->json('ai_metadata')->nullable(); // Provider, model, etc.
            $table->integer('points')->default(10);
            $table->integer('time_limit')->nullable(); // in seconds
            $table->boolean('is_active')->default(true);
            $table->timestamps();
            $table->softDeletes();

            $table->index(['user_id', 'subject_id']);
            $table->index(['type', 'difficulty']);
            $table->index('is_active');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('exercises');
    }
};
