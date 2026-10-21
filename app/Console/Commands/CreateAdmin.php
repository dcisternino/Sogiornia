<?php

namespace App\Console\Commands;

use App\Models\User;
use App\Rules\PasswordLength;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Str;
use Illuminate\Validation\Rules\Password;

/** Crea il primo amministratore senza salvare password nel codice o negli argomenti. */
class CreateAdmin extends Command
{
    protected $signature = 'sogiornia:create-admin {email=cisterninodario@gmail.com} {--name=Dario}';

    protected $description = 'Crea un amministratore chiedendo una password riservata';

    public function handle(): int
    {
        $email = Str::lower(trim((string) $this->argument('email')));
        $name = (string) $this->option('name');

        if (! $this->input->isInteractive()) {
            $this->error('Esegui il comando in un terminale interattivo per scegliere la password.');

            return self::FAILURE;
        }

        if (User::query()->where('email', $email)->exists()) {
            $this->error('Esiste già un utente con questa email. Nessun account modificato.');

            return self::FAILURE;
        }

        $password = $this->secret('Password (almeno 12 caratteri, maiuscole, minuscole e numeri)');
        $confirmation = $this->secret('Conferma password');
        $validator = Validator::make(
            ['name' => $name, 'email' => $email, 'password' => $password, 'password_confirmation' => $confirmation],
            [
                'name' => ['required', 'string', 'max:255'],
                'email' => ['required', 'email', 'max:255'],
                'password' => ['required', 'string', 'confirmed', 'max:255', new PasswordLength, Password::min(12)->mixedCase()->numbers()],
            ],
        );

        if ($validator->fails()) {
            foreach ($validator->errors()->all() as $error) {
                $this->error($error);
            }

            return self::FAILURE;
        }

        $admin = new User(['name' => $name, 'email' => $email, 'password' => $password]);
        $admin->role = User::ROLE_ADMIN;
        $admin->save();
        $this->info('Amministratore creato. Ora puoi accedere dalla pagina login.');

        return self::SUCCESS;
    }
}
