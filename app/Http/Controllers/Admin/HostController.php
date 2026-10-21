<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Http\Requests\Admin\StoreHostRequest;
use App\Models\Room;
use App\Models\Stay;
use App\Models\User;
use App\Services\HostInvitationSender;
use Illuminate\Contracts\View\View;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Str;
use Illuminate\Validation\Rule;

/** Permette all'amministratore di elencare e creare account host. */
class HostController extends Controller
{
    public function index(Request $request): View
    {
        $data = $request->validate(['status' => ['nullable', Rule::in(['active', 'inactive', 'expiring'])]]);
        $status = $data['status'] ?? '';
        $hosts = User::query()->where('role', User::ROLE_HOST);
        if ($status === 'active') {
            $hosts->activeAccess();
        } elseif ($status === 'inactive') {
            $hosts->inactiveAccess();
        } elseif ($status === 'expiring') {
            $hosts->expiringAccess();
        }

        return view('pages.admin.hosts.index', [
            'hosts' => $hosts->with('hostAccess')->withCount('properties')->orderBy('name')->orderBy('id')->paginate(15)->withQueryString(),
            'status' => $status,
        ]);
    }

    /** Mostra dati anagrafici, inventario e prenotazioni senza impersonare l'host. */
    public function show(User $host): View
    {
        abort_unless($host->isHost(), 404);
        $host->load('hostAccess')->loadCount('properties');
        $stays = Stay::query()->whereHas('room.property', fn (Builder $query) => $query->whereBelongsTo($host));

        return view('pages.admin.hosts.show', [
            'host' => $host,
            'roomCount' => Room::query()->whereHas('property', fn (Builder $query) => $query->whereBelongsTo($host))->count(),
            'stayCount' => (clone $stays)->count(),
            'counts' => collect(['active', 'scheduled', 'past'])->mapWithKeys(fn (string $period) => [$period => (clone $stays)->inPeriod($period)->count()]),
            'properties' => $host->properties()->withCount('rooms')->orderBy('name')->orderBy('id')->paginate(10, ['*'], 'properties_page')->withQueryString(),
            'rooms' => Room::query()->whereHas('property', fn (Builder $query) => $query->whereBelongsTo($host))->with('property')->withCount('stays')->orderBy('name')->orderBy('id')->paginate(15, ['*'], 'rooms_page')->withQueryString(),
            'stays' => $stays->with('room.property')->orderByDesc('starts_at')->orderByDesc('id')->paginate(15, ['*'], 'stays_page')->withQueryString(),
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

        return redirect()->route('admin.hosts.show', $host)->with('status', $sender->send($host));
    }
}
