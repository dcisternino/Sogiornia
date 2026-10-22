<?php

namespace App\Services;

use App\Models\Property;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;
use RuntimeException;
use Throwable;
use ZipArchive;

/** Precompila soltanto i dati della struttura in una copia privata del modello Word, senza pubblicare contenuti. */
class GuideWordTemplate
{
    public const VERSION = 1;

    public function create(Property $property): string
    {
        abort_unless(class_exists(ZipArchive::class), 503, 'Il download Word richiede l’estensione PHP ZIP. Contatta l’amministratore.');
        $disk = Storage::disk('local');
        $disk->makeDirectory('guide-templates');
        $path = $disk->path('guide-templates/'.Str::uuid().'.docx');
        if (! copy(resource_path('documents/guest-guide-template.docx'), $path)) {
            throw new RuntimeException('Impossibile preparare il modello Word.');
        }
        $zip = new ZipArchive;
        $opened = false;
        try {
            if ($zip->open($path) !== true) {
                throw new RuntimeException('Impossibile aprire il modello Word.');
            }
            $opened = true;
            $xml = $zip->getFromName('word/document.xml');
            if ($xml === false) {
                throw new RuntimeException('Il modello Word non contiene il documento.');
            }
            $values = [
                '${PROPERTY_NAME}' => $property->name,
                '${PROPERTY_CITY}' => $property->city,
                '${PROPERTY_ADDRESS}' => $property->address ?: '[Compila qui]',
                '${ROOM_NAMES}' => $property->rooms()->whereNull('archived_at')->orderBy('id')->pluck('name')->implode(', ') ?: '[Aggiungi le stanze]',
            ];
            $escaped = array_map(fn (string $value): string => htmlspecialchars($value, ENT_XML1 | ENT_QUOTES | ENT_SUBSTITUTE, 'UTF-8'), $values);
            if (! $zip->addFromString('word/document.xml', strtr($xml, $escaped)) || ! $zip->close()) {
                throw new RuntimeException('Impossibile salvare il modello Word.');
            }
            $opened = false;
        } catch (Throwable $exception) {
            if ($opened) {
                try {
                    $zip->close();
                } catch (Throwable) {
                    // Il file può essere già stato chiuso prima dell'errore.
                }
            }
            $disk->delete('guide-templates/'.basename($path));
            throw $exception;
        }

        return $path;
    }
}
