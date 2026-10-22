<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

/** Crea le traduzioni dei blocchi e trasferisce integralmente i contenuti precedenti. */
return new class extends Migration
{
    public function up(): void
    {
        Schema::create('section_block_translations', function (Blueprint $table): void {
            $table->id();
            $table->foreignId('section_block_id')->constrained()->cascadeOnDelete();
            $table->string('locale', 2);
            $table->string('title')->nullable();
            $table->longText('content')->nullable();
            $table->string('group_title')->nullable();
            $table->string('image_alt')->nullable();
            $table->string('link_label')->nullable();
            $table->timestamps();
            $table->unique(['section_block_id', 'locale'], 'block_translation_locale_unique');
        });
        DB::table('sections')->orderBy('id')->chunkById(100, function (Collection $sections): void {
            foreach ($sections as $section) {
                DB::transaction(function () use ($section): void {
                    if (! $section->content && ! $section->image_path && ! $section->link_url && ! $section->phone) {
                        return;
                    }
                    $id = DB::table('section_blocks')->insertGetId([
                        'section_id' => $section->id, 'type' => 'text', 'title' => $section->title,
                        'content' => $section->content, 'image_path' => $section->image_path,
                        'image_alt' => $section->image_alt, 'link_url' => $section->link_url,
                        'link_label' => $section->link_label, 'phone' => $section->phone,
                        'position' => 0, 'is_active' => true, 'created_at' => now(), 'updated_at' => now(),
                    ]);
                    foreach (DB::table('section_translations')->where('section_id', $section->id)->get() as $translation) {
                        DB::table('section_block_translations')->insert([
                            'section_block_id' => $id, 'locale' => $translation->locale,
                            'title' => $translation->title, 'content' => $translation->content,
                            'image_alt' => $translation->image_alt, 'link_label' => $translation->link_label,
                            'created_at' => now(), 'updated_at' => now(),
                        ]);
                    }
                    DB::table('sections')->where('id', $section->id)->update(['content' => '', 'image_path' => null, 'image_alt' => null, 'link_url' => null, 'link_label' => null, 'phone' => null]);
                    DB::table('section_translations')->where('section_id', $section->id)->update(['content' => null, 'image_alt' => null, 'link_label' => null]);
                });
            }
        });
    }

    /** Il rollback elimina le traduzioni dei blocchi: conservare un backup dei contenuti. */
    public function down(): void
    {
        Schema::dropIfExists('section_block_translations');
    }
};
