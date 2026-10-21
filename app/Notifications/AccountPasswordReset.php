<?php

namespace App\Notifications;

use Illuminate\Auth\Notifications\ResetPassword;
use Illuminate\Notifications\Messages\MailMessage;

/** Invia l'invito iniziale o il recupero password con il token monouso di Laravel. */
class AccountPasswordReset extends ResetPassword
{
    public function toMail($notifiable): MailMessage
    {
        $invitation = $notifiable->hasPendingInvitation();

        return (new MailMessage)
            ->subject($invitation ? 'Il tuo invito a Sogiornia' : 'Recupera la password di Sogiornia')
            ->greeting('Ciao '.$notifiable->name.'!')
            ->line($invitation ? 'Il tuo account host è pronto. Scegli personalmente la tua password per iniziare.' : 'Abbiamo ricevuto una richiesta di recupero della password.')
            ->action($invitation ? 'Scegli la tua password' : 'Imposta una nuova password', rtrim(config('app.url'), '/').route('password.reset', ['token' => $this->token, 'email' => $notifiable->email], false))
            ->line('Il link è valido per '.config('auth.passwords.users.expire').' minuti e può essere utilizzato una sola volta.')
            ->line('Se non aspettavi questo messaggio, puoi ignorarlo.')
            ->salutation('Il team Sogiornia');
    }
}
