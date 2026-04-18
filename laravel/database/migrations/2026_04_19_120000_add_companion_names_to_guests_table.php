<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('guests', function (Blueprint $table): void {
            // Array of companion full names (one entry per extra person beyond the main guest).
            $table->json('companion_names')->nullable()->after('guests_count');
        });
    }

    public function down(): void
    {
        Schema::table('guests', function (Blueprint $table): void {
            $table->dropColumn('companion_names');
        });
    }
};
