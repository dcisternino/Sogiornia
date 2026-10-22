<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

/** Separa lo spazio di lavoro dalla versione pubblicata e protegge le guide preesistenti. */
return new class extends Migration
{
    public function up(): void
    {
        Schema::create('guide_workspaces', function (Blueprint $table): void {
            $table->id();
            $table->foreignId('property_id')->unique()->constrained()->cascadeOnDelete();
            $table->foreignId('published_version_id')->nullable()->constrained('guide_versions')->nullOnDelete();
            $table->unsignedBigInteger('revision')->default(0);
            $table->unsignedBigInteger('lock_sequence')->default(0);
            $table->boolean('needs_initial_version')->default(false);
            $table->timestamps();
        });
        DB::table('properties')->orderBy('id')->chunkById(100, function (Collection $properties): void {
            DB::table('guide_workspaces')->insert($properties->map(fn (object $property): array => ['property_id' => $property->id, 'needs_initial_version' => true, 'revision' => 0, 'lock_sequence' => 0, 'created_at' => now(), 'updated_at' => now()])->all());
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('guide_workspaces');
    }
};
