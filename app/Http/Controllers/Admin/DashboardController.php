<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\HostAccess;
use App\Models\User;
use Illuminate\Contracts\View\View;

/** Mostra lo stato degli host e le scadenze nei prossimi dieci giorni. */
class DashboardController extends Controller
{
    public function __invoke(): View
    {
        $hosts = User::query()->where('role', User::ROLE_HOST);
        $expiring = (clone $hosts)->expiringAccess();

        return view('pages.admin.home', [
            'activeCount' => (clone $hosts)->activeAccess()->count(),
            'inactiveCount' => (clone $hosts)->inactiveAccess()->count(),
            'expiringCount' => (clone $expiring)->count(),
            'hosts' => $expiring->with('hostAccess')->orderBy(HostAccess::query()->select('expires_at')->whereColumn('user_id', 'users.id'))->orderBy('id')->paginate(10),
        ]);
    }
}
