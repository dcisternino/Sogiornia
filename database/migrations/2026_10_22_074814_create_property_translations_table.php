<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Crea le traduzioni opzionali dei contenuti della guida.
     */
    public function up(): void
    {
        Schema::create('property_translations', function (Blueprint $table) {
            $table->id();
            $table->foreignId('property_id')->constrained()->cascadeOnDelete();
            $table->string('locale', 2);
            $table->text('welcome_message')->nullable();
            $table->unique(['property_id', 'locale']);
            $table->timestamps();
        });
    }

    /**
     * Rimuove la tabella delle traduzioni.
     */
    public function down(): void
    {
        Schema::dropIfExists('property_translations');
    }
};
