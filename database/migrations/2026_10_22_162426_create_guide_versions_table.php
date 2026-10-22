<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

/** Conserva istantanee immutabili delle guide approvate. */
return new class extends Migration
{
    public function up(): void
    {
        Schema::create('guide_versions', function (Blueprint $table): void {
            $table->id();
            $table->foreignId('property_id')->constrained()->cascadeOnDelete();
            $table->foreignId('published_by')->nullable()->constrained('users')->nullOnDelete();
            $table->unsignedInteger('number');
            $table->json('payload');
            $table->string('checksum', 64);
            $table->timestamp('published_at');
            $table->timestamps();
            $table->unique(['property_id', 'number']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('guide_versions');
    }
};
