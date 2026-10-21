<?php

namespace App\Http\Requests\Host;

use App\Models\Room;
use Illuminate\Database\Query\Builder;
use Illuminate\Validation\Rule;

/** Riutilizza le date del soggiorno e accetta soltanto stanze non archiviate dell'host. */
class BookingRequest extends StayRequest
{
    public function authorize(): bool
    {
        return $this->user()?->isHost() && $this->user()->canWrite();
    }

    public function rules(): array
    {
        return [...parent::rules(), 'room_id' => ['required', 'integer', Rule::exists(Room::class, 'id')->where(fn (Builder $query) => $query
            ->whereNull('archived_at')->whereIn('property_id', $this->user()->properties()->whereNull('archived_at')->select('id')))]];
    }
}
