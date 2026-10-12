<!DOCTYPE html>
<html lang="it">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <!-- Utilizzo di Bootstrap -->
    @vite(['resources/css/app.css', 'resources/js/app.js'])
    <title>{{$title}}</title>
</head>

<body>
    <nav>
        <a href="{{ route('welcome') }}">Welcome</a>
        <a href="{{ route('admin-home') }}">Admin</a>
        <a href="{{ route('host-home') }}">Host</a>
        <a href="{{ route('guest-home') }}">Guest</a>
    </nav>
    <h1 class="">Home Page Guest</h1>
</body>

</html>