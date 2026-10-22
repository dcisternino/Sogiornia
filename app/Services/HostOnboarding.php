<?php

namespace App\Services;

use App\Models\Section;
use App\Models\User;
use Illuminate\Database\Eloquent\Builder;

/** Organizza i sei passaggi sui dati reali, distinguendo preparazione, Word facoltativo e condivisione. */
class HostOnboarding
{
    public function __construct(private GuidePublication $publication, private GuideSnapshot $snapshots, private PublishedGuideContent $publishedContent) {}

    /** @return array<string, mixed> */
    public function forHost(User $user, ?int $propertyId = null, ?int $roomId = null, array $downloadedTemplates = []): array
    {
        $properties = $user->properties()->whereNull('archived_at')->orderBy('id')->get();
        $property = $propertyId ? $properties->firstWhere('id', $propertyId) : $properties->first();
        $rooms = $property?->rooms()->whereNull('archived_at')->orderBy('id')->get() ?? collect();
        $room = $roomId ? $rooms->firstWhere('id', $roomId) : $rooms->first();
        $hasContent = $room && Section::query()->visibleToRoom($room)->where(function (Builder $query): void {
            $query->where('content', '!=', '')->orWhereNotNull('phone')->orWhereNotNull('link_url')->orWhereNotNull('image_path')->orWhereHas('blocks', fn (Builder $query) => $query->where('is_active', true));
        })->exists();
        $stay = $room?->stays()->whereNull('revoked_at')->where('ends_at', '>', now())->orderBy('starts_at')->orderBy('id')->first();
        $published = $property ? $this->publication->workspace($property)->publishedVersion : null;
        $hasPublishedRoom = $room && $published && collect($published->payload['rooms'])->contains('id', $room->id);
        $hasChanges = $property && (! $published || $published->checksum !== $this->snapshots->checksum($this->snapshots->capture($property)));
        $hasPublishedContent = $hasPublishedRoom && collect($this->publishedContent->selectRoom($published->payload, $room->id)['sections'])->contains(fn (array $section): bool => filled($section['content']) || count($section['blocks']) > 0 || filled($section['phone']) || filled($section['link_url']) || filled($section['image_path']));
        $guideEnabled = $property && $room && $property->guide_enabled && $room->guide_enabled;
        $canShare = $stay && $guideEnabled && $hasPublishedContent;
        $wordDownloaded = $property && ($downloadedTemplates[$property->id] ?? null) === GuideWordTemplate::VERSION;
        $context = array_filter(['from' => 'setup', 'setup_room_id' => $room?->id]);
        $link = fn (string $name, array $parameters = []): string => route($name, array_merge($parameters, $context));
        $action = fn (string $label, string $url, bool $write = true): array => ['label' => $label, 'url' => $url, 'write' => $write];
        $steps = [
            ['title' => 'Crea la struttura', 'description' => 'Nome, posizione e messaggio di benvenuto.', 'complete' => (bool) $property, 'counts_progress' => true, 'actions' => [$action($property ? 'Modifica struttura' : 'Crea struttura', $property ? $link('host.properties.edit', [$property]) : $link('host.properties.create'))]],
            ['title' => 'Aggiungi le stanze', 'description' => 'Configura la stanza selezionata oppure aggiungine altre alla stessa struttura.', 'complete' => (bool) $room, 'counts_progress' => true, 'actions' => $property ? array_values(array_filter([$room ? $action('Configura stanza', $link('host.rooms.edit', [$property, $room])) : null, $action('Aggiungi stanza', $link('host.rooms.create', [$property]))])) : []],
            ['title' => 'Prepara la guida con il template Word', 'description' => 'Scarica il modello, compilalo e raccogli tutte le informazioni. Puoi anche proseguire inserendole direttamente nel portale.', 'complete' => (bool) $wordDownloaded, 'counts_progress' => false, 'status' => $wordDownloaded ? 'Modello scaricato' : 'Facoltativo', 'actions' => $property ? [$action('Scarica template Word', $link('host.guide-template.download', [$property]), false), $action('Come compilarlo', $link('host.guide-template.show', [$property]), false)] : []],
            ['title' => 'Prepara le sezioni', 'description' => 'Organizza i contenuti, scegli le stanze che li vedranno e approva la pubblicazione della guida.', 'complete' => (bool) ($hasContent && $hasPublishedRoom && ! $hasChanges), 'counts_progress' => true, 'actions' => $property ? array_values(array_filter([$action('Gestisci sezioni', $link('host.sections.index', [$property]), false), $room ? $action('Scegli sezioni della stanza', $link('host.rooms.edit', [$property, $room])) : null, $room ? $action('Anteprima guida', $link('host.preview.index', [$property, $room]), false) : null, $action('Rivedi e pubblica', $link('host.guide.show', [$property]), false)])) : []],
            ['title' => 'Crea un soggiorno', 'description' => 'Imposta arrivo e partenza. Il link sarà valido soltanto durante il soggiorno.', 'complete' => (bool) $stay, 'counts_progress' => true, 'actions' => $room ? [$action('Nuovo soggiorno', $link('host.stays.create', [$property, $room]))] : []],
            ['title' => 'Condividi la guida', 'description' => 'Scegli la lingua, controlla il messaggio e apri WhatsApp o email. La preparazione del messaggio non registra un invio avvenuto.', 'complete' => false, 'counts_progress' => false, 'status' => $canShare ? 'Pronta da condividere' : 'Da preparare', 'actions' => $canShare ? [$action('Prepara messaggio', $link('host.stays.share', [$property, $room, $stay]), false)] : []],
        ];
        $requiredSteps = collect($steps)->where('counts_progress', true);

        return compact('properties', 'property', 'rooms', 'room', 'hasContent', 'stay', 'guideEnabled', 'hasPublishedRoom', 'hasChanges', 'steps', 'canShare') + ['completed' => $requiredSteps->where('complete', true)->count(), 'total' => $requiredSteps->count()];
    }
}
