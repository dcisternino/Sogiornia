<?php

namespace App\Http\Requests\Host;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

/** Valida soltanto i testi italiani inviati dal modulo per produrre una bozza senza salvarla. */
class TranslationSuggestionRequest extends FormRequest
{
    public function authorize(): bool
    {
        return $this->user()?->isHost() && $this->user()->canWrite();
    }

    public function rules(): array
    {
        $fields = match ($this->input('type')) {
            'property' => ['welcome_message'],
            'section' => ['title', 'content'],
            'block' => ['title', 'content', 'group_title', 'image_alt', 'link_label'],
            default => [],
        };
        $rules = ['type' => ['required', 'string', Rule::in(['property', 'section', 'block'])], 'locale' => ['required', 'string', Rule::in(['en', 'es', 'de'])], 'fields' => ['required', 'array:'.implode(',', $fields), 'min:1']];
        foreach ($fields as $field) {
            $rules['fields.'.$field] = ['sometimes', 'required', 'string', 'max:'.match ($field) {
                'content' => 20000, 'welcome_message' => 3000, default => 255
            }];
        }

        return $rules;
    }

    public function attributes(): array
    {
        return [
            'fields.title' => 'titolo italiano',
            'fields.content' => 'descrizione italiana',
            'fields.welcome_message' => 'benvenuto italiano',
            'fields.group_title' => 'titolo di gruppo italiano',
            'fields.image_alt' => 'descrizione italiana della foto',
            'fields.link_label' => 'testo italiano del pulsante',
        ];
    }
}
