<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

/**
 * DJ song recommendations submitted by guests (optionally linked to a guest row).
 * When submitted anonymously (no session guest), the `guest_id` stays null and
 * `submitted_by` carries a manually-provided name.
 */
return new class extends Migration
{
    public function up(): void
    {
        Schema::create('song_recommendations', function (Blueprint $table) {
            $table->id();
            $table->foreignId('guest_id')
                ->nullable()
                ->constrained('guests')
                ->nullOnDelete();
            $table->string('submitted_by', 120)->nullable();
            $table->string('title', 200);
            $table->string('artist', 200)->nullable();
            $table->string('notes', 500)->nullable();
            $table->string('session_token', 64)->nullable()->index();
            $table->timestamps();

            $table->index(['guest_id', 'created_at']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('song_recommendations');
    }
};
