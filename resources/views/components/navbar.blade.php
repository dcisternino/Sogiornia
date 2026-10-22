{{-- Navigazione pubblica e accesso alla dashboard del proprio ruolo. --}}
<nav class="navbar navbar-expand-xl sticky-top" aria-label="Navigazione principale">
    <div class="container">
        {{-- Logo completo con collegamento alla pagina iniziale. --}}
        <a class="navbar-brand">
            <img
                src="{{ asset('image/sogiornia-logo.png') }}"
                alt="Sogiornia"
                style="width: 180px; max-width: 100%; height: auto;">
        </a>
        <button class="navbar-toggler" type="button" data-bs-toggle="collapse" data-bs-target="#mainNavbar" aria-controls="mainNavbar" aria-expanded="false" aria-label="Apri navigazione">
            <span class="navbar-toggler-icon"></span>
        </button>
        <div class="collapse navbar-collapse" id="mainNavbar">
            <ul class="navbar-nav ms-auto align-items-xl-center gap-xl-2">
                @guest
                <li class="nav-item"><a class="nav-link" href="{{ route('welcome') }}#vantaggi">Perché Sogiornia</a></li>
                <li class="nav-item"><a class="nav-link" href="{{ route('welcome') }}#come-funziona">Come funziona</a></li>
                <li class="nav-item"><a class="nav-link" href="{{ route('welcome') }}#prezzi">Prezzi</a></li>
                <li class="nav-item"><a class="btn btn-primary btn-sm px-4" href="{{ route('login') }}">Accedi <span aria-hidden="true">↗</span></a></li>
                @else
                {{-- Menu distinto per ruolo con indicazione della pagina corrente. --}}
                <li class="nav-item"><a class="nav-link {{ request()->routeIs('admin-home', 'host-home') ? 'active' : '' }}" @if(request()->routeIs('admin-home', 'host-home')) aria-current="page" @endif href="{{ route(auth()->user()->isAdmin() ? 'admin-home' : 'host-home') }}">Dashboard</a></li>
                @if (auth()->user()->isAdmin())
                    <li class="nav-item"><a class="nav-link {{ request()->routeIs('admin.hosts.*') ? 'active' : '' }}" @if(request()->routeIs('admin.hosts.*')) aria-current="page" @endif href="{{ route('admin.hosts.index') }}">Host</a></li>
                    <li class="nav-item"><a class="nav-link {{ request()->routeIs('admin.monitoring.*') ? 'active' : '' }}" @if(request()->routeIs('admin.monitoring.*')) aria-current="page" @endif href="{{ route('admin.monitoring.index') }}">Monitoraggio</a></li>
                @else
                    <li class="nav-item"><a class="nav-link {{ request()->routeIs('host.calendar.*', 'host.bookings.*', 'host.stays.*') ? 'active' : '' }}" @if(request()->routeIs('host.calendar.*', 'host.bookings.*', 'host.stays.*')) aria-current="page" @endif href="{{ route('host.calendar.index') }}">Soggiorni</a></li>
                    <li class="nav-item"><a class="nav-link {{ request()->routeIs('host.properties.*') ? 'active' : '' }}" @if(request()->routeIs('host.properties.*')) aria-current="page" @endif href="{{ route('host.properties.index') }}">Strutture</a></li>
                    <li class="nav-item"><a class="nav-link {{ request()->routeIs('host.rooms.*', 'host.room-directory.*') ? 'active' : '' }}" @if(request()->routeIs('host.rooms.*', 'host.room-directory.*')) aria-current="page" @endif href="{{ route('host.room-directory.index') }}">Stanze</a></li>
                    <li class="nav-item"><a class="nav-link {{ request()->routeIs('host.sections.*', 'host.section-directory.*', 'host.blocks.*') ? 'active' : '' }}" @if(request()->routeIs('host.sections.*', 'host.section-directory.*', 'host.blocks.*')) aria-current="page" @endif href="{{ route('host.section-directory.index') }}">Sezioni</a></li>
                @endif
                <li class="nav-item"><a class="nav-link {{ request()->routeIs('profile.*') ? 'active' : '' }}" @if(request()->routeIs('profile.*')) aria-current="page" @endif href="{{ route('profile.edit') }}">Profilo</a></li>
                @if(auth()->user()->isHost())
                    {{-- Aiuto accessibile: unico ingresso alla configurazione guidata. --}}
                    <li class="nav-item"><a class="nav-link navbar-help {{ request()->routeIs('host.setup.*') ? 'active' : '' }}" @if(request()->routeIs('host.setup.*')) aria-current="page" @endif href="{{ route('host.setup.index') }}" aria-label="Aiuto: configurazione guidata" title="Configurazione guidata"><svg width="23" height="23" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.7" aria-hidden="true" focusable="false"><circle cx="12" cy="12" r="9"/><path d="M9.5 8.8a2.5 2.5 0 0 1 5 .3c0 1.7-2.5 2-2.5 3.9"/><circle cx="12" cy="16.7" r=".8" fill="currentColor" stroke="none"/></svg><span class="d-xl-none">Aiuto</span></a></li>
                @endif
                <li class="nav-item">
                    <form method="POST" action="{{ route('logout') }}">
                        @csrf
                        <button class="btn btn-outline-secondary btn-sm" type="submit">Esci</button>
                    </form>
                </li>
                @endguest
            </ul>
        </div>
    </div>
</nav>
