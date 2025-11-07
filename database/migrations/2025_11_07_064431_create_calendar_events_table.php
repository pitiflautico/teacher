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
        Schema::create('calendar_events', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->constrained()->cascadeOnDelete();
            $table->foreignId('subject_id')->nullable()->constrained()->nullOnDelete();

            $table->string('title');
            $table->text('description')->nullable();
            $table->enum('type', ['class', 'exam', 'task', 'study', 'other'])->default('other');

            $table->dateTime('start_at');
            $table->dateTime('end_at')->nullable();
            $table->boolean('all_day')->default(false);

            $table->string('location')->nullable();
            $table->string('color', 7)->nullable();

            // Google Calendar sync
            $table->string('google_event_id')->nullable()->unique();
            $table->timestamp('synced_at')->nullable();

            $table->boolean('reminder_enabled')->default(true);
            $table->integer('reminder_minutes')->default(30);

            $table->timestamps();
            $table->softDeletes();

            $table->index(['user_id', 'start_at']);
            $table->index(['type', 'start_at']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('calendar_events');
    }
};
