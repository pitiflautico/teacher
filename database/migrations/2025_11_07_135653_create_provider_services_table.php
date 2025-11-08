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
        Schema::create('provider_services', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_ai_provider_id')->constrained('user_ai_providers')->cascadeOnDelete();
            $table->string('service_type'); // 'ocr', 'chat', 'image_recognition', 'image_generation'
            $table->string('model')->nullable(); // Specific model to use for this service
            $table->boolean('is_active')->default(true);
            $table->json('configuration')->nullable(); // Additional service-specific config
            $table->timestamps();

            $table->unique(['user_ai_provider_id', 'service_type']);
            $table->index(['user_ai_provider_id', 'service_type', 'is_active'], 'provider_services_lookup');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('provider_services');
    }
};
