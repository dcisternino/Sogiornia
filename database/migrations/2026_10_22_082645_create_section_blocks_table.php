<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

/** Crea i contenuti ordinabili delle sezioni senza modificare le tabelle esistenti. */
return new class extends Migration
{
    public function up(): void
    {
        Schema::create('section_blocks', function (Blueprint $table): void {
            $table->id();
            $table->foreignId('section_id')->constrained()->cascadeOnDelete();
            $table->string('type', 20);
            $table->string('title');
            $table->longText('content')->nullable();
            $table->string('group_title')->nullable();
            $table->string('image_path')->nullable();
            $table->string('image_alt')->nullable();
            $table->string('link_url', 2048)->nullable();
            $table->string('link_label')->nullable();
            $table->string('phone', 40)->nullable();
            $table->string('address', 500)->nullable();
            $table->boolean('is_active')->default(true);
            $table->unsignedInteger('position')->default(0);
            $table->timestamps();
            $table->index(['section_id', 'position']);
        });
    }

    /** Il rollback elimina i contenuti aggiunti: eseguire un backup prima di annullare. */
    public function down(): void
    {
        Schema::dropIfExists('section_blocks');
    }
};
