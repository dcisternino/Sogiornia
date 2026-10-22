<?php

namespace App\Http\Requests\Admin;

use Illuminate\Foundation\Http\FormRequest;

/** Valida le eccezioni di budget e la sospensione delle traduzioni per il singolo host. */
class HostTranslationLimitRequest extends FormRequest
{
    public function authorize(): bool
    {
        return $this->user()?->isAdmin() ?? false;
    }

    public function rules(): array
    {
        return ['is_enabled' => ['required', 'boolean'], 'uses_default_limit' => ['required', 'boolean'], 'monthly_character_limit' => ['nullable', 'integer', 'min:0', 'max:100000000000']];
    }

    public function attributes(): array
    {
        return ['monthly_character_limit' => 'limite mensile personalizzato', 'uses_default_limit' => 'uso del limite predefinito'];
    }
}
