<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('photos', function (Blueprint $table) {
            $table->string('google_drive_file_id')->nullable()->after('synced_to_google_photos_at');
            $table->timestamp('synced_to_google_drive_at')->nullable()->after('google_drive_file_id');
        });
    }

    public function down(): void
    {
        Schema::table('photos', function (Blueprint $table) {
            $table->dropColumn(['google_drive_file_id', 'synced_to_google_drive_at']);
        });
    }
};
