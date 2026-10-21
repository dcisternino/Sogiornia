<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

// Associa ogni stanza alle sole sezioni selezionate dall'host.
return new class extends Migration
{
    public function up(): void
    {
        Schema::create('room_section', function (Blueprint $table): void {
            $table->foreignId('room_id')->constrained()->cascadeOnDelete();
            $table->foreignId('section_id')->constrained()->cascadeOnDelete();
            $table->timestamps();
            $table->primary(['room_id', 'section_id']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('room_section');
    }
};
