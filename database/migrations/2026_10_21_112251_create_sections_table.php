<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

// Crea i contenuti informativi con slug univoci all'interno della struttura.
return new class extends Migration
{
    public function up(): void
    {
        Schema::create('sections', function (Blueprint $table): void {
            $table->id();
            $table->foreignId('property_id')->constrained()->cascadeOnDelete();
            $table->string('title');
            $table->string('slug', 100);
            $table->mediumText('content');
            $table->string('image_path')->nullable();
            $table->string('image_alt')->nullable();
            $table->string('link_url', 2048)->nullable();
            $table->string('link_label')->nullable();
            $table->string('phone', 40)->nullable();
            $table->timestamps();
            $table->unique(['property_id', 'slug']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('sections');
    }
};
