<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /** Crea descrizione e foto della struttura senza alterare le tabelle esistenti. */
    public function up(): void
    {
        Schema::create('property_profiles', function (Blueprint $table) {
            $table->id();
            $table->foreignId('property_id')->unique()->constrained()->cascadeOnDelete();
            $table->text('description')->nullable();
            $table->string('image_path')->nullable();
            $table->timestamps();
        });
    }

    /** Annulla la creazione della tabella dei profili struttura. */
    public function down(): void
    {
        Schema::dropIfExists('property_profiles');
    }
};
