<?php

namespace App\Http\Requests\Admin;

use Illuminate\Foundation\Http\FormRequest;

/** Consente soltanto all'admin di impostare interruttore globale e soglie mensili. */
class TranslationSettingRequest extends FormRequest
{
    public function authorize(): bool
    {
        return $this->user()?->isAdmin() ?? false;
    }

    public function rules(): array
    {
        return ['is_enabled' => ['required', 'boolean'], 'monthly_character_limit' => ['nullable', 'integer', 'min:0', 'max:100000000000'], 'default_host_character_limit' => ['nullable', 'integer', 'min:0', 'max:100000000000']];
    }

    public function attributes(): array
    {
        return ['monthly_character_limit' => 'limite mensile globale', 'default_host_character_limit' => 'limite mensile predefinito per host'];
    }
}
