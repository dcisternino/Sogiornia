<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use App\Models\User;
use App\Rules\PasswordLength;
use Illuminate\Auth\Events\PasswordReset;
use Illuminate\Contracts\View\View;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Password;
use Illuminate\Support\Str;
use Illuminate\Validation\Rules\Password as PasswordRule;

/** Completa invito o recupero tramite token monouso e invalida le vecchie sessioni. */
class NewPasswordController extends Controller
{
    public function create(Request $request, string $token): View
    {
        return view('auth.reset-password', ['token' => $token, 'email' => $request->query('email', '')]);
    }

    public function store(Request $request): RedirectResponse
    {
        $data = $request->validate([
            'token' => ['required', 'string'],
            'email' => ['required', 'email', 'max:255'],
            'password' => ['required', 'string', 'confirmed', 'max:255', new PasswordLength, PasswordRule::min(12)->mixedCase()->numbers()],
        ]);
        $data['email'] = mb_strtolower($data['email']);
        $status = Password::reset($data, function (User $user, string $password): void {
            DB::transaction(function () use ($user, $password): void {
                $user->password = $password;
                $user->remember_token = Str::random(60);
                if ($user->hasPendingInvitation()) {
                    $user->invitation_accepted_at = now();
                    $user->email_verified_at = now();
                }
                $user->save();
                DB::table('sessions')->where('user_id', $user->id)->delete();
            });
            event(new PasswordReset($user));
        });

        return $status === Password::PASSWORD_RESET
            ? redirect()->route('login')->with('status', 'Password salvata. Ora puoi accedere.')
            : back()->withErrors(['email' => 'Il link non è valido o è scaduto. Richiedine uno nuovo.'])->withInput($request->only('email'));
    }
}
