<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use Illuminate\Contracts\View\View;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Password;
use Throwable;

/** Avvia il recupero senza rivelare se un indirizzo email è registrato. */
class PasswordResetLinkController extends Controller
{
    public function create(): View
    {
        return view('auth.forgot-password');
    }

    public function store(Request $request): RedirectResponse
    {
        $data = $request->validate(['email' => ['required', 'email', 'max:255']]);
        $data['email'] = mb_strtolower($data['email']);
        try {
            Password::sendResetLink($data);
        } catch (Throwable $exception) {
            report($exception);
        }

        return back()->with('status', 'Se l’indirizzo è registrato, riceverai un link per scegliere la password. Controlla anche la posta indesiderata.');
    }
}
