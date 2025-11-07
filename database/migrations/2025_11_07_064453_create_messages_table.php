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
        Schema::create('messages', function (Blueprint $table) {
            $table->id();
            $table->foreignId('sender_id')->constrained('users')->cascadeOnDelete();
            $table->foreignId('recipient_id')->constrained('users')->cascadeOnDelete();
            $table->foreignId('group_id')->nullable()->constrained()->cascadeOnDelete();

            $table->text('message');
            $table->string('attachment')->nullable();
            $table->enum('attachment_type', ['file', 'image', 'video'])->nullable();

            $table->timestamp('read_at')->nullable();
            $table->boolean('is_group_message')->default(false);

            $table->timestamps();
            $table->softDeletes();

            $table->index(['sender_id', 'recipient_id']);
            $table->index(['group_id', 'created_at']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('messages');
    }
};
