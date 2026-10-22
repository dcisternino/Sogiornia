<?php

namespace App\Http\Requests\Host;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Support\Facades\Gate;

/** Valida i dati della struttura e verifica la proprietà durante la modifica. */
class PropertyRequest extends FormRequest
{
    public function authorize(): bool
    {
        $property = $this->route('property');

        return ($this->user()?->canWrite() ?? false)
            && ($property === null || Gate::allows('update', $property));
    }

    /** @return array<string, mixed> */
    public function rules(): array
    {
        return [
            'name' => ['required', 'string', 'max:255'],
            'city' => ['required', 'string', 'max:255'],
            'address' => ['nullable', 'string', 'max:255'],
            'welcome_message' => ['nullable', 'string', 'max:3000'],
            'description' => ['nullable', 'string', 'max:500'],
            'image' => ['nullable', 'file', 'image', 'mimes:jpg,jpeg,png,webp', 'max:5120', 'dimensions:max_width=4000,max_height=4000'],
            'remove_image' => ['sometimes', 'boolean', 'prohibited_with:image'],
            'translations' => ['sometimes', 'array:en,es,de'],
            'translations.*' => ['array:welcome_message'],
            'translations.*.welcome_message' => ['nullable', 'string', 'max:3000'],
        ];
    }
}
