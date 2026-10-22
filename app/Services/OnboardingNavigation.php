<?php

namespace App\Services;

use App\Models\Property;
use App\Models\Room;
use Illuminate\Http\Request;

/** Conserva la provenienza dal percorso guidato usando soltanto destinazioni interne autorizzate. */
class OnboardingNavigation
{
    private array $returnUrls = [];

    public function __construct(private Request $request) {}

    public function active(): bool
    {
        return $this->request->input('from') === 'setup';
    }

    public function parameters(): array
    {
        if (! $this->active()) {
            return [];
        }
        $room = $this->request->route('room');
        $value = $room instanceof Room ? $room->id : ($room ?? $this->request->input('setup_room_id'));
        $roomId = is_scalar($value) ? filter_var($value, FILTER_VALIDATE_INT, ['options' => ['min_range' => 1]]) : false;

        return array_filter(['from' => 'setup', 'setup_room_id' => $roomId ?: null], fn (mixed $value): bool => $value !== null);
    }

    public function url(string $name, mixed $parameters = []): string
    {
        $parameters = is_array($parameters) ? $parameters : [$parameters];

        return route($name, array_merge($parameters, $this->parameters()));
    }

    public function setupUrl(?Property $property = null, ?Room $room = null): string
    {
        $property ??= $this->request->route('property') instanceof Property ? $this->request->route('property') : null;
        if (! $property || $property->user_id !== $this->request->user()?->id) {
            return route('host.setup.index');
        }
        $roomId = $room?->id ?? ($this->parameters()['setup_room_id'] ?? null);
        $cacheKey = $property->id.':'.($roomId ?? '').':'.($property->archived_at ? 'archived' : 'active');
        if (isset($this->returnUrls[$cacheKey])) {
            return $this->returnUrls[$cacheKey];
        }
        $roomId = $roomId ? $property->rooms()->whereKey($roomId)->whereNull('archived_at')->value('id') : null;

        return $this->returnUrls[$cacheKey] = route('host.setup.index', array_filter(['property_id' => $property->archived_at ? null : $property->id, 'room_id' => $property->archived_at ? null : $roomId]));
    }

    public function back(string $name, mixed $parameters = []): string
    {
        return $this->active() ? $this->setupUrl() : route($name, $parameters);
    }

    public function carry(string $location): string
    {
        if (! $this->active() || ! str_starts_with($location, url('/host/')) || str_starts_with($location, route('host.setup.index'))) {
            return $location;
        }
        $parts = parse_url($location);
        parse_str($parts['query'] ?? '', $query);
        $base = explode('?', explode('#', $location, 2)[0], 2)[0];

        return $base.'?'.http_build_query(array_merge($query, $this->parameters())).(isset($parts['fragment']) ? '#'.$parts['fragment'] : '');
    }
}
