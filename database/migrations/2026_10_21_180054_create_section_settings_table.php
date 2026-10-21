<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /** Crea le impostazioni facoltative di attivazione e condivisione delle sezioni. */
    public function up(): void
    {
        Schema::create('section_settings', function (Blueprint $table) {
            $table->id();
            $table->foreignId('section_id')->unique()->constrained()->cascadeOnDelete();
            $table->boolean('is_active')->default(true);
            $table->boolean('is_shared')->default(false);
            $table->timestamps();
        });
    }

    /** Annulla la creazione della tabella delle impostazioni. */
    public function down(): void
    {
        Schema::dropIfExists('section_settings');
    }
};
