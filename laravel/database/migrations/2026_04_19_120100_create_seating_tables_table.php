<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('seating_tables', function (Blueprint $table): void {
            $table->id();
            $table->string('label');
            $table->unsignedSmallInteger('capacity')->nullable();
            $table->unsignedSmallInteger('sort_order')->default(0);
            $table->text('notes')->nullable();
            $table->timestamps();

            $table->index('sort_order');
        });

        Schema::table('guests', function (Blueprint $table): void {
            $table->foreignId('seating_table_id')
                ->nullable()
                ->after('companion_names')
                ->constrained('seating_tables')
                ->nullOnDelete();
        });
    }

    public function down(): void
    {
        Schema::table('guests', function (Blueprint $table): void {
            $table->dropForeign(['seating_table_id']);
            $table->dropColumn('seating_table_id');
        });

        Schema::dropIfExists('seating_tables');
    }
};
