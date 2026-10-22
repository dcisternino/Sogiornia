<?php

namespace App\Http\Requests\Host;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Support\Facades\Gate;

/** Richiede l'approvazione esplicita della revisione della propria struttura. */
class PublishGuideRequest extends FormRequest
{
    public function authorize(): bool
    {
        return Gate::allows('update', $this->route('property'));
    }

    public function rules(): array
    {
        return ['revision' => ['required', 'integer', 'min:0'], 'base_version' => ['nullable', 'integer', 'min:1'], 'approval' => ['required', 'accepted']];
    }

    public function messages(): array
    {
        return ['approval.required' => 'Conferma di avere controllato e approvato la bozza.', 'approval.accepted' => 'Conferma di avere controllato e approvato la bozza.'];
    }
}
