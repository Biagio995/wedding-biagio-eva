<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

/**
 * Per-gift optional message left by the guest who claims the item (keepsake
 * note for the couple — "saw this and thought of you", etc.).
 */
return new class extends Migration
{
    public function up(): void
    {
        Schema::table('registry_items', function (Blueprint $table) {
            $table->text('claim_message')->nullable()->after('claimed_by_name');
        });
    }

    public function down(): void
    {
        Schema::table('registry_items', function (Blueprint $table) {
            $table->dropColumn('claim_message');
        });
    }
};
