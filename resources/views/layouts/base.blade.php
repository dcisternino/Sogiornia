{{-- Struttura HTML comune a pagine pubbliche, dashboard e guida ospite. --}}
<!DOCTYPE html>
<html lang="it">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="description" content="@yield('description', 'Sogiornia: la guida digitale per accogliere i tuoi ospiti con cura.')">
    <meta name="referrer" content="@yield('referrer', 'same-origin')">
    <title>@yield('title', 'La tua ospitalità, sempre presente') | {{ config('app.name', 'Sogiornia') }}</title>
    @vite(['resources/css/app.css', 'resources/js/app.js'])
</head>
<body class="@yield('body-class')">
    <a class="visually-hidden-focusable skip-link" href="#main-content">Vai al contenuto</a>
    @yield('body')
</body>
</html>
