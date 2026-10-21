@extends('layouts.guest')

@section('title', 'Casa Marina')

@section('content')

<div class="container py-5">

    <!-- Intestazione struttura -->
    <section class="text-center mb-5">

        <h1>Casa Marina</h1>

        <p class="text-muted-custom mt-2">
            Benvenuto nella tua casa a Bari!
        </p>

    </section>

    <!-- Sezioni informative -->
    <section class="row g-3">

        @foreach ($sections as $slug => $section)

        <div class="col-12 col-md-6">

            <a
                href="{{ route('guest.section', ['section' => $slug]) }}"
                class="sg-card h-100 text-center d-block">

                <!-- Icona -->
                <div class="fs-2 mb-2">
                    {{ $section['icon'] }}
                </div>

                <!-- Titolo -->
                <h2 class="h5 mb-0">
                    {{ $section['title'] }}
                </h2>

            </a>

        </div>

        @endforeach

    </section>

</div>

@endsection