<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /** Crea la scadenza facoltativa degli host senza modificare le tabelle esistenti. */
    public function up(): void
    {
        Schema::create('host_accesses', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->unique()->constrained()->cascadeOnDelete();
            $table->timestamp('expires_at')->nullable()->index();
            $table->timestamps();
        });
    }

    /** Annulla esclusivamente la creazione di questa tabella. */
    public function down(): void
    {
        Schema::dropIfExists('host_accesses');
    }
};
