@extends('layouts.app')
@section('title', 'Dashboard host')

{{-- Dashboard essenziale, pronta per i futuri indicatori e grafici. --}}
@section('content')
<div class="container py-5 workspace">
    <div class="page-heading">
        <div><span class="eyebrow">IL TUO SPAZIO HOST</span><h1 class="mt-2">Il tuo spazio per accogliere.</h1><p class="text-muted-custom">Ciao {{ auth()->user()->name }}, benvenuto nel tuo spazio host.</p></div>
        @if(auth()->user()->canWrite())<a class="btn btn-primary" href="{{ route('host.bookings.create') }}">+ Nuovo soggiorno</a>@endif
    </div>
</div>
@endsection
