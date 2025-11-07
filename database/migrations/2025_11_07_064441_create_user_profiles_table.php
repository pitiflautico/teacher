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
        Schema::create('user_profiles', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->unique()->constrained()->cascadeOnDelete();

            $table->string('avatar')->nullable();
            $table->text('bio')->nullable();
            $table->string('location')->nullable();
            $table->string('website')->nullable();
            $table->string('twitter')->nullable();
            $table->string('linkedin')->nullable();

            // Study preferences
            $table->enum('study_schedule', ['morning', 'afternoon', 'evening', 'night'])->default('afternoon');
            $table->integer('daily_goal_minutes')->default(120);

            // AI preferences
            $table->enum('preferred_ai_provider', ['openai', 'replicate', 'together'])->default('openai');
            $table->integer('ai_creativity')->default(7); // 0-10
            $table->enum('ai_tone', ['formal', 'casual', 'friendly', 'professional'])->default('friendly');

            // Privacy
            $table->boolean('profile_public')->default(true);
            $table->boolean('show_progress')->default(true);
            $table->boolean('show_badges')->default(true);

            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('user_profiles');
    }
};
