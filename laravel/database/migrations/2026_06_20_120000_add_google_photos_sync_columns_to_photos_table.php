<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('photos', function (Blueprint $table) {
            $table->string('google_photos_media_id')->nullable()->after('approved');
            $table->timestamp('synced_to_google_photos_at')->nullable()->after('google_photos_media_id');
        });
    }

    public function down(): void
    {
        Schema::table('photos', function (Blueprint $table) {
            $table->dropColumn(['google_photos_media_id', 'synced_to_google_photos_at']);
        });
    }
};
