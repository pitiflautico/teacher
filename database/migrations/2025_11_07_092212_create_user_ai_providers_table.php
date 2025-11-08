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
        Schema::create('user_ai_providers', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->constrained()->cascadeOnDelete();
            $table->string('provider'); // 'openai', 'anthropic', 'google'
            $table->string('api_key'); // Encrypted
            $table->boolean('is_active')->default(true);
            $table->integer('token_limit')->nullable(); // Max tokens per month
            $table->decimal('cost_limit', 10, 2)->nullable(); // Max cost per month
            $table->bigInteger('tokens_used')->default(0); // Tokens used this month
            $table->decimal('cost_spent', 10, 2)->default(0); // Cost spent this month
            $table->timestamp('usage_reset_at')->nullable(); // When usage resets
            $table->timestamps();

            $table->unique(['user_id', 'provider']);
            $table->index(['user_id', 'is_active']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('user_ai_providers');
    }
};
