<?php

namespace App\Services;

/** Riassume differenze e contenuti senza modificare le versioni pubblicate. */
class GuideReview
{
    public function __construct(private GuideSnapshot $snapshots) {}

    public function changes(?array $published, array $draft): array
    {
        $changes = [];
        if ($this->snapshots->checksum($published['property'] ?? []) !== $this->snapshots->checksum($draft['property'])) {
            $changes[] = 'Dati, benvenuto, foto o traduzioni della struttura';
        }
        foreach (['rooms' => 'Stanza', 'sections' => 'Sezione'] as $key => $label) {
            $before = collect($published[$key] ?? [])->keyBy('id');
            $after = collect($draft[$key])->keyBy('id');
            foreach ($after as $id => $item) {
                if ($this->snapshots->checksum($before->get($id, [])) !== $this->snapshots->checksum($item)) {
                    $changes[] = $label.': '.($item['title'] ?? $item['name']).($before->has($id) ? ' (modificata)' : ' (nuova)');
                }
            }
            foreach ($before->diffKeys($after) as $item) {
                $changes[] = $label.': '.($item['title'] ?? $item['name']).' (rimossa dalla bozza)';
            }
        }

        return $changes;
    }
}
