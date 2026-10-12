<nav class="navbar navbar-expand-lg">
    <div class="container">
        <a class="navbar-brand fw-semibold" href="{{ route('welcome') }}">
            Sogiornia
        </a>

        <button
            class="navbar-toggler"
            type="button"
            data-bs-toggle="collapse"
            data-bs-target="#mainNavbar"
            aria-controls="mainNavbar"
            aria-expanded="false"
            aria-label="Apri navigazione">
            <span class="navbar-toggler-icon"></span>
        </button>

        <div class="collapse navbar-collapse" id="mainNavbar">
            <ul class="navbar-nav ms-auto gap-lg-3">
                <li class="nav-item">
                    <a class="nav-link {{ request()->routeIs('welcome') ? 'active' : '' }}"
                        href="{{ route('welcome') }}">
                        Welcome
                    </a>
                </li>

                <li class="nav-item">
                    <a class="nav-link {{ request()->routeIs('admin-home') ? 'active' : '' }}"
                        href="{{ route('admin-home') }}">
                        Admin
                    </a>
                </li>

                <li class="nav-item">
                    <a class="nav-link {{ request()->routeIs('host-home') ? 'active' : '' }}"
                        href="{{ route('host-home') }}">
                        Host
                    </a>
                </li>

                <li class="nav-item">
                    <a class="nav-link {{ request()->routeIs('guest-home') ? 'active' : '' }}"
                        href="{{ route('guest-home') }}">
                        Guest
                    </a>
                </li>

            </ul>
        </div>
    </div>
</nav>