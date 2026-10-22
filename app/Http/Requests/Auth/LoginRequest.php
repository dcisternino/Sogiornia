<?php

namespace App\Http\Requests\Auth;

use App\Rules\PasswordLength;
use Illuminate\Support\Str;
use Laravel\Fortify\Http\Requests\LoginRequest as FortifyLoginRequest;

/** Estende la richiesta Fortify mantenendo normalizzazione e limiti delle credenziali. */
class LoginRequest extends FortifyLoginRequest
{
    public function authorize(): bool
    {
        return true;
    }

    protected function prepareForValidation(): void
    {
        if (is_string($this->input('email'))) {
            $this->merge(['email' => Str::lower(trim($this->input('email')))]);
        }
    }

    /** @return array<string, mixed> */
    public function rules(): array
    {
        return [
            'email' => ['required', 'string', 'email', 'max:255'],
            'password' => ['required', 'string', 'max:255', new PasswordLength],
            'remember' => ['sometimes', 'boolean'],
        ];
    }
}
