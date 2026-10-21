<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

// Crea le stanze con un link ospite casuale e un nome univoco nella struttura.
return new class extends Migration
{
    public function up(): void
    {
        Schema::create('rooms', function (Blueprint $table): void {
            $table->id();
            $table->foreignId('property_id')->constrained()->cascadeOnDelete();
            $table->string('name');
            $table->text('description')->nullable();
            $table->timestamp('archived_at')->nullable();
            $table->boolean('guide_enabled')->default(true);
            $table->timestamps();
            $table->unique(['property_id', 'name']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('rooms');
    }
};
