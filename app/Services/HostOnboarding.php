<?php

namespace App\Services;

use App\Models\Section;
use App\Models\User;
use Illuminate\Database\Eloquent\Builder;

/** Calcola i passaggi di configurazione sui dati reali della struttura e della stanza scelte. */
class HostOnboarding
{
    public function __construct(private GuidePublication $publication) {}

    /** @return array<string, mixed> */
    public function forHost(User $user, ?int $propertyId = null, ?int $roomId = null): array
    {
        $properties = $user->properties()->whereNull('archived_at')->orderBy('id')->get();
        $property = $propertyId ? $properties->firstWhere('id', $propertyId) : $properties->first();
        $rooms = $property?->rooms()->whereNull('archived_at')->orderBy('id')->get() ?? collect();
        $room = $roomId ? $rooms->firstWhere('id', $roomId) : $rooms->first();
        $hasContent = $room && Section::query()->visibleToRoom($room)->where(function (Builder $query): void {
            $query->where('content', '!=', '')->orWhereHas('blocks', fn (Builder $query) => $query->where('is_active', true));
        })->exists();
        $stay = $room?->stays()->whereNull('revoked_at')->where('ends_at', '>', now())->orderBy('starts_at')->orderBy('id')->first();
        $published = $property ? $this->publication->workspace($property)->publishedVersion : null;
        $hasPublishedRoom = $room && $published && collect($published->payload['rooms'])->contains('id', $room->id);
        $guideEnabled = $property && $room && $property->guide_enabled && $room->guide_enabled;
        $steps = [
            ['title' => 'Crea la struttura', 'description' => 'Nome, posizione e messaggio di benvenuto.', 'complete' => (bool) $property, 'url' => $property ? route('host.properties.edit', $property) : route('host.properties.create'), 'action' => $property ? 'Modifica struttura' : 'Crea struttura'],
            ['title' => 'Aggiungi una stanza', 'description' => 'Inserisci il nome e scegli una foto per la guida.', 'complete' => (bool) $room, 'url' => $room ? route('host.rooms.edit', [$property, $room]) : ($property ? route('host.rooms.create', $property) : null), 'action' => $room ? 'Configura stanza' : 'Aggiungi stanza'],
            ['title' => 'Prepara le sezioni', 'description' => 'Aggiungi i contenuti e condividi le sezioni oppure abilitale nella stanza scelta.', 'complete' => (bool) $hasContent, 'url' => $property ? route('host.sections.index', $property) : null, 'action' => 'Gestisci sezioni'],
            ['title' => 'Rivedi e pubblica la guida', 'description' => 'Verifica la bozza e approva i contenuti per gli ospiti.', 'complete' => (bool) $hasPublishedRoom, 'url' => $property ? route('host.guide.show', $property) : null, 'action' => 'Rivedi e pubblica'],
            ['title' => 'Crea un soggiorno', 'description' => 'Imposta arrivo e partenza: il link sarà valido soltanto durante queste date.', 'complete' => (bool) $stay, 'url' => $room ? route('host.stays.create', [$property, $room]) : null, 'action' => 'Nuovo soggiorno'],
        ];

        return compact('properties', 'property', 'rooms', 'room', 'hasContent', 'stay', 'guideEnabled', 'hasPublishedRoom', 'steps') + ['completed' => collect($steps)->where('complete', true)->count()];
    }
}
