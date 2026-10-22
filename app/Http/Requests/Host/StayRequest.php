<?php

namespace App\Http\Requests\Host;

use Carbon\CarbonImmutable;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Support\Facades\Gate;

/** Valida le date locali del soggiorno e impedisce nuove prenotazioni su stanze archiviate. */
class StayRequest extends FormRequest
{
    public function authorize(): bool
    {
        return Gate::allows('update', $this->route('property'));
    }

    /** @return array<string, mixed> */
    public function rules(): array
    {
        return [
            'reference' => ['required', 'string', 'max:120'],
            'arrival_date' => ['required', 'date_format:Y-m-d'],
            'departure_date' => ['required', 'date_format:Y-m-d', 'after:arrival_date'],
            'arrival_time' => ['required', 'date_format:H:i'],
            'overlap_confirmed' => ['sometimes', 'boolean'],
        ];
    }

    /** @return array<string, mixed> */
    public function stayData(): array
    {
        $data = $this->validated();

        return [
            'reference' => $data['reference'],
            'starts_at' => CarbonImmutable::createFromFormat('!Y-m-d H:i', $data['arrival_date'].' '.$data['arrival_time'], 'Europe/Rome')->utc(),
            'ends_at' => CarbonImmutable::createFromFormat('!Y-m-d H:i', $data['departure_date'].' 10:00', 'Europe/Rome')->utc(),
        ];
    }
}
