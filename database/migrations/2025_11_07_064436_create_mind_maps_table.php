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
        Schema::create('mind_maps', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->constrained()->cascadeOnDelete();
            $table->foreignId('subject_id')->nullable()->constrained()->nullOnDelete();
            $table->foreignId('topic_id')->nullable()->constrained()->nullOnDelete();
            $table->foreignId('material_id')->nullable()->constrained()->nullOnDelete();

            $table->string('title');
            $table->text('description')->nullable();

            // Mind map data as JSON
            $table->longText('nodes_data'); // JSON: [{id, label, x, y, color, ...}]
            $table->longText('edges_data')->nullable(); // JSON: [{from, to, label, ...}]

            $table->string('thumbnail')->nullable();
            $table->boolean('is_public')->default(false);
            $table->integer('views_count')->default(0);

            $table->timestamps();
            $table->softDeletes();

            $table->index(['user_id', 'subject_id']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('mind_maps');
    }
};
