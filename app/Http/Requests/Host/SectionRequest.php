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
        ];
    }
}
