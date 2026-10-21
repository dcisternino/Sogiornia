<?php

namespace App\Http\Requests\Admin;

use Illuminate\Foundation\Http\FormRequest;

/** Riserva all'amministratore la scelta della scadenza dell'host. */
class HostAccessRequest extends FormRequest
{
    public function authorize(): bool
    {
        return $this->user()?->isAdmin() && $this->route('host')->isHost();
    }

    public function rules(): array
    {
        return ['expires_on' => ['nullable', 'date_format:Y-m-d']];
    }
}
