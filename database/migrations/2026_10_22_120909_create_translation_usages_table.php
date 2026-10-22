<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

/** Registra soltanto metadati e prenotazioni del budget, senza testi o credenziali. */
return new class extends Migration
{
    public function up(): void
    {
        Schema::create('translation_usages', function (Blueprint $table): void {
            $table->id();
            $table->foreignId('user_id')->nullable()->constrained()->nullOnDelete();
            $table->date('period');
            $table->string('resource_type', 20);
            $table->string('locale', 2);
            $table->unsignedInteger('characters');
            $table->unsignedInteger('budget_characters');
            $table->string('status', 20);
            $table->string('error_code', 40)->nullable();
            $table->unsignedSmallInteger('provider_status')->nullable();
            $table->unsignedInteger('duration_ms')->nullable();
            $table->timestamps();
            $table->index(['period', 'id']);
            $table->index(['user_id', 'period']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('translation_usages');
    }
};
