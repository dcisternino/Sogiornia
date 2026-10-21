<?php

namespace App\Http\Requests\Host;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Support\Facades\Gate;
use Illuminate\Validation\Rule;

/** Impedisce di assegnare a una stanza sezioni appartenenti ad altre strutture. */
class RoomRequest extends FormRequest
{
    public function authorize(): bool
    {
        return Gate::allows('update', $this->route('property'));
    }

    /** @return array<string, mixed> */
    public function rules(): array
    {
        $property = $this->route('property');
        $room = $this->route('room');
        $roomId = $room === null ? null : $property->rooms()->findOrFail($room)->id;

        return [
            'name' => ['required', 'string', 'max:255', Rule::unique('rooms')->where('property_id', $property->id)->ignore($roomId)],
            'description' => ['nullable', 'string', 'max:3000'],
            'sections' => ['nullable', 'array', 'max:100'],
            'sections.*' => ['required', 'integer', 'distinct', Rule::exists('sections', 'id')->where('property_id', $property->id)],
        ];
    }
}
