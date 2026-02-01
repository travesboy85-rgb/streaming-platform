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
        Schema::table('videos', function (Blueprint $table) {
            // ✅ Add status column for approval workflow
            $table->enum('status', ['pending', 'approved'])
                  ->default('pending')
                  ->after('metadata');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('videos', function (Blueprint $table) {
            // ✅ Drop status column if rolled back
            $table->dropColumn('status');
        });
    }
};

