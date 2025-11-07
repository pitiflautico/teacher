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
        Schema::create('groups', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->constrained()->cascadeOnDelete(); // creator
            $table->foreignId('subject_id')->nullable()->constrained()->nullOnDelete();

            $table->string('name');
            $table->text('description')->nullable();
            $table->string('avatar')->nullable();
            $table->string('cover_image')->nullable();

            $table->enum('visibility', ['public', 'private'])->default('public');
            $table->boolean('requires_approval')->default(false);

            $table->integer('members_count')->default(1);
            $table->integer('posts_count')->default(0);

            $table->timestamps();
            $table->softDeletes();

            $table->index(['visibility', 'created_at']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('groups');
    }
};
