<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

/** Crea le eccezioni per host: soglia personalizzata e sospensione delle sole traduzioni. */
return new class extends Migration
{
    public function up(): void
    {
        Schema::create('host_translation_limits', function (Blueprint $table): void {
            $table->id();
            $table->foreignId('user_id')->unique()->constrained()->cascadeOnDelete();
            $table->boolean('is_enabled')->default(true);
            $table->boolean('uses_default_limit')->default(true);
            $table->unsignedBigInteger('monthly_character_limit')->nullable();
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('host_translation_limits');
    }
};
