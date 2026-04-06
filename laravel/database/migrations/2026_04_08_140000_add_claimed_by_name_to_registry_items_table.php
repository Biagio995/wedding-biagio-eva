<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('registry_items', function (Blueprint $table) {
            $table->string('claimed_by_name', 255)->nullable()->after('claimed_at');
        });
    }

    public function down(): void
    {
        Schema::table('registry_items', function (Blueprint $table) {
            $table->dropColumn('claimed_by_name');
        });
    }
};
