@extends('layouts.guest')

@section('title', $section['title'])

@section('content')

<div class="container py-5">

    <a href="{{ route('guest-home') }}">
        ← Torna alla guida
    </a>

    <div class="sg-card mt-4">

        <h1>
            {{ $section['icon'] }}
            {{ $section['title'] }}
        </h1>

        @foreach ($section['content'] as $label => $value)

        <div class="mb-3">

            <strong>{{ $label }}</strong>

            <p class="mb-0">
                {{ $value }}
            </p>

        </div>

        @endforeach

    </div>

</div>

@endsection