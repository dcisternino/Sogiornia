<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

/** Crea le soglie globali e inizializza il limite condiviso da tutti gli host. */
return new class extends Migration
{
    public function up(): void
    {
        Schema::create('translation_settings', function (Blueprint $table): void {
            $table->id();
            $table->boolean('is_enabled')->default(true);
            $table->unsignedBigInteger('monthly_character_limit')->nullable();
            $table->unsignedBigInteger('default_host_character_limit')->nullable();
            $table->unsignedBigInteger('reservation_sequence')->default(0);
            $table->timestamps();
        });
        DB::table('translation_settings')->insert(['id' => 1, 'is_enabled' => true, 'monthly_character_limit' => 450000, 'default_host_character_limit' => 30000, 'reservation_sequence' => 0, 'created_at' => now(), 'updated_at' => now()]);
    }

    public function down(): void
    {
        Schema::dropIfExists('translation_settings');
    }
};
