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
        Schema::create('videos', function (Blueprint $table) {
            $table->id();
            $table->string('title');
            $table->text('description')->nullable();

            // Support both external URLs and local uploads
            $table->string('url')->nullable();       // External video URL
            $table->string('file_path')->nullable(); // Local uploaded file path

            $table->string('thumbnail')->nullable(); // Thumbnail image URL
            $table->unsignedInteger('duration')->default(0); // Duration in seconds
            $table->foreignId('category_id')->nullable()->constrained()->nullOnDelete();
            $table->foreignId('user_id')->constrained()->cascadeOnDelete(); // Uploader
            $table->boolean('is_premium')->default(false); // Premium content
            $table->unsignedBigInteger('views')->default(0);
            $table->json('metadata')->nullable(); // Additional video info
            $table->timestamps();
            $table->softDeletes(); // For soft deletion

            // ✅ Indexes for performance
            $table->index('user_id');
            $table->index('category_id');
            $table->index('is_premium');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('videos');
    }
};

