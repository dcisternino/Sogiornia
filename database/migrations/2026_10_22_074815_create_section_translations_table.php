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
        Schema::create('section_translations', function (Blueprint $table) {
            $table->id();
            $table->foreignId('section_id')->constrained()->cascadeOnDelete();
            $table->string('locale', 2);
            $table->string('title')->nullable();
            $table->longText('content')->nullable();
            $table->string('image_alt')->nullable();
            $table->string('link_label')->nullable();
            $table->unique(['section_id', 'locale']);
            $table->timestamps();
        });
    }

    /**
     * Rimuove la tabella delle traduzioni.
     */
    public function down(): void
    {
        Schema::dropIfExists('section_translations');
    }
};
