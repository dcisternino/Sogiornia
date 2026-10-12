<!DOCTYPE html>
<html lang="it">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">

    <title>@yield('title', 'Sogiornia') | Sogiornia</title>

    @vite(['resources/css/app.css', 'resources/js/app.js'])
</head>

<body>

    <main>
        <x-navbar />
        @yield('content')
    </main>

</body>

</html>