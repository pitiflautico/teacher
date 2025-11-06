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
        Schema::create('materials', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->constrained()->onDelete('cascade');
            $table->foreignId('subject_id')->nullable()->constrained()->onDelete('set null');
            $table->foreignId('topic_id')->nullable()->constrained()->onDelete('set null');
            $table->string('title');
            $table->text('description')->nullable();
            $table->enum('type', ['document', 'image', 'pdf', 'link', 'note'])->default('note');
            $table->string('file_path')->nullable(); // Ruta del archivo
            $table->string('original_filename')->nullable();
            $table->string('mime_type')->nullable();
            $table->unsignedBigInteger('file_size')->nullable(); // en bytes
            $table->longText('extracted_text')->nullable(); // Texto extraído por OCR
            $table->json('ai_metadata')->nullable(); // Metadata de IA (tags, clasificación automática, etc.)
            $table->boolean('is_processed')->default(false); // Indica si fue procesado por OCR/IA
            $table->dateTime('processed_at')->nullable();
            $table->timestamps();
            $table->softDeletes();

            $table->index(['user_id', 'subject_id']);
            $table->index(['user_id', 'topic_id']);
            $table->index('type');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('materials');
    }
};
