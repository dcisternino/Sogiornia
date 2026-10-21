{{-- Navigazione pubblica e accesso alla dashboard del proprio ruolo. --}}
<nav class="navbar navbar-expand-lg sticky-top" aria-label="Navigazione principale">
    <div class="container">
        {{-- Logo completo con collegamento alla pagina iniziale. --}}
        <a class="navbar-brand" href="{{ route('welcome') }}">
            <img
                src="{{ asset('image/sogiornia-logo.png') }}"
                alt="Sogiornia"
                style="width: 180px; max-width: 100%; height: auto;">
        </a>
        <button class="navbar-toggler" type="button" data-bs-toggle="collapse" data-bs-target="#mainNavbar" aria-controls="mainNavbar" aria-expanded="false" aria-label="Apri navigazione">
            <span class="navbar-toggler-icon"></span>
        </button>
        <div class="collapse navbar-collapse" id="mainNavbar">
            <ul class="navbar-nav ms-auto align-items-lg-center gap-lg-3">
                @guest
                <li class="nav-item"><a class="nav-link" href="{{ route('welcome') }}#vantaggi">Perché Sogiornia</a></li>
                <li class="nav-item"><a class="nav-link" href="{{ route('welcome') }}#come-funziona">Come funziona</a></li>
                <li class="nav-item"><a class="nav-link" href="{{ route('welcome') }}#prezzi">Prezzi</a></li>
                <li class="nav-item"><a class="btn btn-primary btn-sm px-4" href="{{ route('login') }}">Accedi <span aria-hidden="true">↗</span></a></li>
                @else
                <li class="nav-item"><span class="nav-link small">{{ auth()->user()->name }}</span></li>
                <li class="nav-item"><a class="nav-link" href="{{ route('dashboard') }}">La mia dashboard</a></li>
                <li class="nav-item"><a class="nav-link" href="{{ route('profile.edit') }}">Il mio profilo</a></li>
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