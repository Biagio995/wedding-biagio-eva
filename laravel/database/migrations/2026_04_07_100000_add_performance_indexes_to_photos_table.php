<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

/**
 * US-30: speed up public album listings (filter by approval + sort by id; filter by upload day).
 */
return new class extends Migration
{
    public function up(): void
    {
        Schema::table('photos', function (Blueprint $table) {
            $table->index(['approved', 'id']);
            $table->index('created_at');
        });
    }

    public function down(): void
    {
        Schema::table('photos', function (Blueprint $table) {
            $table->dropIndex(['approved', 'id']);
            $table->dropIndex(['created_at']);
        });
    }
};
