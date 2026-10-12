@extends('layouts.app')

@section('title', 'Welcome')

@section('content')

<div class="container py-5">
    <section class="text-center">
        <h1>Benvenuto su Sogiornia</h1>

        <p class="text-muted-custom mt-3">
            Tutte le informazioni per un soggiorno perfetto,
            in un unico posto.
        </p>

        <a href="{{ route('host-home') }}" class="sg-btn-primary d-inline-block mt-3">
            Accedi come Host
        </a>
    </section>
</div>

@endsection