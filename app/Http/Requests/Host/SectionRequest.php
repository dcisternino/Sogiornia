<?php

namespace App\Http\Requests\Host;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Support\Facades\Gate;
use Illuminate\Support\Str;
use Illuminate\Validation\Rule;

/** Valida testo e slug delle sezioni della struttura posseduta dall'host. */
class SectionRequest extends FormRequest
{
    public function authorize(): bool
    {
        return Gate::allows('update', $this->route('property'));
    }

    protected function prepareForValidation(): void
    {
        if (! $this->filled('slug') && is_string($this->input('title'))) {
            $this->merge(['slug' => Str::slug($this->input('title'))]);
        }
    }

    /** @return array<string, mixed> */
    public function rules(): array
    {
        $property = $this->route('property');
        $section = $this->route('section');
        $sectionId = $section === null ? null : $property->sections()->findOrFail($section)->id;

        return [
            'title' => ['required', 'string', 'max:255'],
            'slug' => ['required', 'string', 'max:100', 'regex:/^[a-z0-9]+(?:-[a-z0-9]+)*$/', Rule::unique('sections')->where('property_id', $property->id)->ignore($sectionId)],
            'content' => ['required', 'string', 'max:20000'],
            'image' => ['nullable', 'file', 'image', 'mimes:jpg,jpeg,png,webp', 'max:5120', 'dimensions:max_width=4000,max_height=4000'],
            'remove_image' => ['sometimes', 'boolean'],
            'image_alt' => ['nullable', 'string', 'max:255'],
            'link_url' => ['nullable', 'url:http,https', 'max:2048'],
            'link_label' => ['nullable', 'string', 'max:255'],
            'phone' => ['nullable', 'string', 'max:40', 'regex:/^\\+?[0-9][0-9 ()-]{2,39}$/'],
            'translations' => ['sometimes', 'array:en,es,de'],
            'translations.*' => ['array:title,content,image_alt,link_label'],
            'translations.*.title' => ['nullable', 'string', 'max:255'],
            'translations.*.content' => ['nullable', 'string', 'max:20000'],
            'translations.*.image_alt' => ['nullable', 'string', 'max:255'],
            'translations.*.link_label' => ['nullable', 'string', 'max:255'],
        ];
    }

    public function messages(): array
    {
        return ['phone.regex' => 'Inserisci un numero telefonico valido, con eventuale prefisso + e separatori.'];
    }
}
