@extends('layouts.app')
@section('title', 'Versione '.$version->number.' · '.$property->name)
{{-- Consultazione privata di una versione approvata, senza modificarla o ripubblicarla. --}}
@section('content')
<div class="container py-5 workspace">
    <a class="back-link" href="{{ route('host.guide.show', $property) }}">← Revisione e pubblicazione</a>
    <h1 class="mt-4">Versione {{ $version->number }}</h1>
    <p class="text-muted-custom">Pubblicata il {{ $version->published_at->timezone('Europe/Rome')->format('d/m/Y H:i') }} · {{ $version->publisher?->name ?? 'Versione iniziale conservata' }}. Questa copia è immutabile.</p>
    <div class="sg-card"><x-guide-content :payload="$draft" /></div>
</div>
@endsection
