<?php

namespace App\Services;

use App\Models\Room;
use App\Models\Stay;
use Illuminate\Support\Facades\DB;
use Illuminate\Validation\ValidationException;

/** Serializza le prenotazioni della stanza e richiede conferma per periodi sovrapposti. */
class StayScheduler
{
    /** @param array{reference: string, starts_at: mixed, ends_at: mixed} $data */
    public function save(Room $room, array $data, bool $confirmed, ?Stay $stay = null): Stay
    {
        return DB::transaction(function () use ($room, $data, $confirmed, $stay): Stay {
            $lockedRoom = Room::query()->lockForUpdate()->findOrFail($room->id);
            $conflicts = $lockedRoom->stays()->where('starts_at', '<', $data['ends_at'])->where('ends_at', '>', $data['starts_at']);
            if ($stay) {
                $conflicts->whereKeyNot($stay->id);
            }
            if (! $confirmed && $conflicts->exists()) {
                throw ValidationException::withMessages(['overlap' => 'Il periodo selezionato si sovrappone a un altro soggiorno di questa stanza. Controlla le date oppure conferma esplicitamente la sovrapposizione.']);
            }
            if ($stay) {
                $stay->update($data);

                return $stay;
            }

            return $lockedRoom->stays()->create($data);
        }, 3);
    }
}
