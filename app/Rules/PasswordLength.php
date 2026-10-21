<?php

namespace App\Rules;

use Closure;
use Illuminate\Contracts\Validation\ValidationRule;

/** Evita errori di hashing e troncamenti delle password oltre il limite di bcrypt. */
class PasswordLength implements ValidationRule
{
    public function validate(string $attribute, mixed $value, Closure $fail): void
    {
        if (config('hashing.driver', 'bcrypt') === 'bcrypt' && is_string($value) && strlen($value) > 72) {
            $fail('La password è troppo lunga. Scegli una password più breve.');
        }
    }
}
