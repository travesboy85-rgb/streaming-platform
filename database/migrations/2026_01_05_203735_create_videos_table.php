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

            // Relationships
            $table->foreignId('category_id')->nullable()->constrained()->nullOnDelete();
            $table->foreignId('user_id')->constrained()->cascadeOnDelete(); // Uploader

            // Flags and counters
            $table->boolean('is_premium')->default(false); // Premium content
            $table->unsignedBigInteger('views')->default(0);
            $table->unsignedBigInteger('likes')->default(0); // ✅ Added likes counter
            $table->json('metadata')->nullable(); // Additional video info

            // ✅ Approval workflow
            $table->enum('status', ['pending', 'approved'])->default('pending');

            $table->timestamps();
            $table->softDeletes(); // For soft deletion

            // ✅ Indexes for performance
            $table->index('user_id');
            $table->index('category_id');
            $table->index('is_premium');
            $table->index('status');
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



