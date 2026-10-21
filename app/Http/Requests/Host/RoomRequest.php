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
            'image' => ['nullable', 'file', 'image', 'mimes:jpg,jpeg,png,webp', 'max:5120', 'dimensions:max_width=4000,max_height=4000'],
            'remove_image' => ['sometimes', 'boolean', 'prohibited_with:image'],
            'sections' => ['nullable', 'array', 'max:100'],
            'sections.*' => ['required', 'integer', 'distinct', Rule::exists('sections', 'id')->where('property_id', $property->id)],
        ];
    }

    /** Restituisce indicazioni concrete per i problemi più frequenti del modulo. */
    public function messages(): array
    {
        return [
            'name.unique' => 'Questa struttura ha già una stanza con questo nome. Scegli un nome diverso.',
            'image.max' => 'La foto supera 5 MB. Scegli un file più leggero e riprova.',
            'image.mimes' => 'Carica una foto JPEG, PNG o WebP.',
            'sections.*.exists' => 'Una delle sezioni selezionate non appartiene a questa struttura. Ricarica la pagina e seleziona le sezioni disponibili.',
        ];
    }
}
