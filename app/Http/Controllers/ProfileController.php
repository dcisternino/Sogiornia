<?php

namespace App\Http\Controllers;

use App\Rules\PasswordLength;
use Illuminate\Contracts\View\View;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;
use Illuminate\Validation\Rule;
use Illuminate\Validation\Rules\Password;

/** Consente di aggiornare il proprio profilo dopo conferma della password corrente. */
class ProfileController extends Controller
{
    public function edit(Request $request): View
    {
        return view('profile.edit', ['user' => $request->user()]);
    }

    public function update(Request $request): RedirectResponse
    {
        abort_unless($request->user()->canWrite(), 403);
        $data = $request->validate([
            'name' => ['required', 'string', 'max:255'],
            'email' => ['required', 'email', 'lowercase', 'max:255', Rule::unique('users')->ignore($request->user()->id)],
            'current_password' => ['required', 'current_password'],
            'password' => ['nullable', 'string', 'confirmed', 'max:255', new PasswordLength, Password::min(12)->mixedCase()->numbers()],
        ]);
        $user = $request->user();
        if ($data['email'] !== $user->email) {
            $user->email_verified_at = null;
            DB::table('password_reset_tokens')->where('email', $user->email)->delete();
        }
        $user->fill(['name' => $data['name'], 'email' => $data['email']]);
        if (! empty($data['password'])) {
            $user->password = $data['password'];
            $user->remember_token = Str::random(60);
            DB::table('sessions')->where('user_id', $user->id)->where('id', '!=', $request->session()->getId())->delete();
        }
        $user->save();
        $request->session()->regenerate();

        return back()->with('status', 'Profilo aggiornato.');
    }
}
