<?php

namespace App\Services;

use App\Models\User;
use App\Rules\PasswordLength;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Validator;
use Illuminate\Validation\Rules\Password;
use Laravel\Fortify\Contracts\ResetsUserPasswords;

/** Completa invito o recupero tramite Fortify, mantenendo verifica email e revoca delle sessioni. */
class ResetUserPassword implements ResetsUserPasswords
{
    /** @param array<string, mixed> $input */
    public function reset(User $user, array $input): void
    {
        Validator::make($input, [
            'password' => ['required', 'string', 'confirmed', 'max:255', new PasswordLength, Password::min(12)->mixedCase()->numbers()],
        ])->validate();
        DB::transaction(function () use ($user, $input): void {
            $user->password = $input['password'];
            if ($user->hasPendingInvitation()) {
                $user->invitation_accepted_at = now();
                $user->email_verified_at = now();
            }
            $user->save();
            DB::table('sessions')->where('user_id', $user->id)->delete();
        });
        // Fortify completa l'operazione ruotando il remember token ed emettendo PasswordReset.
    }
}
