<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

// Crea soggiorni con link riservati, scadenza automatica e revoca anticipata.
return new class extends Migration
{
    public function up(): void
    {
        Schema::create('stays', function (Blueprint $table): void {
            $table->id();
            $table->foreignId('room_id')->constrained()->cascadeOnDelete();
            $table->string('reference', 120);
            $table->string('guide_token', 64)->unique();
            $table->timestamp('starts_at');
            $table->timestamp('ends_at');
            $table->timestamp('revoked_at')->nullable();
            $table->timestamps();
            $table->index(['room_id', 'ends_at']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('stays');
    }
};
