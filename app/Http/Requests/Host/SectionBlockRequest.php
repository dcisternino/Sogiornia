<?php

namespace App\Http\Requests\Host;

use App\Models\SectionBlock;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Support\Facades\Gate;
use Illuminate\Validation\Rule;

/** Valida i contenuti del blocco e limita la scrittura alla struttura posseduta. */
class SectionBlockRequest extends FormRequest
{
    public function authorize(): bool
    {
        return Gate::allows('update', $this->route('property'));
    }

    public function rules(): array
    {
        return [
            'type' => ['required', Rule::in(array_keys(SectionBlock::TYPES))],
            'title' => ['required', 'string', 'max:255'],
            'content' => ['nullable', 'string', 'max:20000'],
            'group_title' => ['nullable', 'string', 'max:255'],
            'address' => ['nullable', 'string', 'max:500'],
            'is_active' => ['required', 'boolean'],
            'phone' => ['required_if:type,contact', 'nullable', 'string', 'max:40', 'regex:/^\+?[0-9][0-9 ()-]{2,39}$/'],
            'link_url' => ['required_if:type,link', 'nullable', 'url:http,https', 'max:2048'],
            'link_label' => ['nullable', 'string', 'max:255'],
            'image' => ['nullable', 'file', 'image', 'mimes:jpg,jpeg,png,webp', 'max:5120', 'dimensions:max_width=4000,max_height=4000'],
            'remove_image' => ['sometimes', 'boolean'],
            'image_alt' => ['nullable', 'string', 'max:255'],
            'translations' => ['sometimes', 'array:en,es,de'],
            'translations.*' => ['array:title,content,group_title,image_alt,link_label'],
            'translations.*.title' => ['nullable', 'string', 'max:255'],
            'translations.*.content' => ['nullable', 'string', 'max:20000'],
            'translations.*.group_title' => ['nullable', 'string', 'max:255'],
            'translations.*.image_alt' => ['nullable', 'string', 'max:255'],
            'translations.*.link_label' => ['nullable', 'string', 'max:255'],
        ];
    }

    public function messages(): array
    {
        return ['phone.required_if' => 'Per un contatto inserisci il numero da chiamare.', 'phone.regex' => 'Inserisci un numero telefonico valido.', 'link_url.required_if' => 'Per un collegamento inserisci un indirizzo web.'];
    }
}
