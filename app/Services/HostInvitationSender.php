<?php

namespace App\Services;

use App\Models\User;
use Illuminate\Support\Facades\Password;
use Throwable;

/** Centralizza l'invio e permette di riprovare senza creare account duplicati. */
class HostInvitationSender
{
    public function send(User $host): string
    {
        try {
            $status = Password::sendResetLink(['email' => $host->email]);
            if ($status === Password::RESET_THROTTLED) {
                return 'Un link è stato richiesto da poco. Attendi un minuto prima di riprovare.';
            }
            if ($status !== Password::RESET_LINK_SENT) {
                return 'Invito non inviato. Puoi riprovare dall’elenco host.';
            }

            return in_array(config('mail.default'), ['log', 'array'], true)
                ? 'Account creato. Configura il servizio email per recapitare l’invito; in sviluppo il messaggio viene registrato localmente.'
                : 'Invito inviato. L’host sceglierà personalmente la password.';
        } catch (Throwable $exception) {
            report($exception);

            return 'Account presente, ma invio email non riuscito. Controlla la configurazione email e usa Reinvia invito.';
        }
    }
}
