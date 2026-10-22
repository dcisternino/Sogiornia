<?php

namespace App\Http\Requests\Host;

use App\Models\Property;
use App\Models\Room;
use Illuminate\Database\Query\Builder;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

/** Valida i filtri del calendario mantenendoli confinati alle strutture dell'host. */
class StayFilterRequest extends FormRequest
{
    public function authorize(): bool
    {
        return $this->user()?->isHost() ?? false;
    }

    public function rules(): array
    {
        return [
            'period' => ['nullable', Rule::in(['active', 'scheduled', 'past', 'all'])],
            'property_id' => ['nullable', 'integer', Rule::exists(Property::class, 'id')->where('user_id', $this->user()->id)],
            'room_id' => ['nullable', 'integer', Rule::exists(Room::class, 'id')->where(function (Builder $query): void {
                $query->whereIn('property_id', $this->user()->properties()->select('id'));
                if ($this->filled('property_id')) {
                    $query->where('property_id', $this->integer('property_id'));
                }
            })],
            'from_date' => ['nullable', 'date_format:Y-m-d'],
            'to_date' => ['nullable', 'date_format:Y-m-d', Rule::when($this->filled('from_date'), ['after_or_equal:from_date'])],
        ];
    }
}
