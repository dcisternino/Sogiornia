<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\User;
use App\Services\HostInvitationSender;
use Illuminate\Http\RedirectResponse;

/** Reinvia l'invito soltanto agli host che non lo hanno ancora accettato. */
class HostInvitationController extends Controller
{
    public function store(User $host, HostInvitationSender $sender): RedirectResponse
    {
        abort_unless($host->isHost() && $host->hasPendingInvitation(), 404);

        return back()->with('status', $sender->send($host));
    }
}
