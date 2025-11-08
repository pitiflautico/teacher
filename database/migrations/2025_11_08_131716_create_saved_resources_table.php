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
        Schema::create('saved_resources', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->constrained()->cascadeOnDelete();
            $table->foreignId('subject_id')->nullable()->constrained()->nullOnDelete();
            $table->string('title');
            $table->text('url');
            $table->text('snippet')->nullable();
            $table->string('type'); // pdf, video, exercise, article
            $table->integer('relevance')->default(0);
            $table->text('ai_reason')->nullable();
            $table->string('source')->default('web_search'); // where it was found
            $table->text('notes')->nullable(); // user's personal notes
            $table->boolean('is_favorite')->default(false);
            $table->timestamp('accessed_at')->nullable(); // last time user opened it
            $table->timestamps();

            $table->index(['user_id', 'type']);
            $table->index(['user_id', 'subject_id']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('saved_resources');
    }
};
