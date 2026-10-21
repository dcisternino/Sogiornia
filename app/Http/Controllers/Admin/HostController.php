<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Http\Requests\Admin\StoreHostRequest;
use App\Models\User;
use App\Services\HostInvitationSender;
use Illuminate\Contracts\View\View;
use Illuminate\Http\RedirectResponse;
use Illuminate\Support\Str;

/** Permette all'amministratore di elencare e creare account host. */
class HostController extends Controller
{
    public function index(): View
    {
        return view('pages.admin.home', [
            'hosts' => User::query()->where('role', User::ROLE_HOST)->withCount('properties')->orderByDesc('id')->paginate(15),
            'hostCount' => User::query()->where('role', User::ROLE_HOST)->count(),
        ]);
    }

    public function create(): View
    {
        return view('pages.admin.hosts.create');
    }

    public function store(StoreHostRequest $request, HostInvitationSender $sender): RedirectResponse
    {
        $host = new User($request->safe()->only(['name', 'email']));
        $host->password = Str::random(48);
        $host->invited_at = now();
        $host->role = User::ROLE_HOST;
        $host->save();

        return redirect()->route('admin-home')->with('status', $sender->send($host));
    }
}
