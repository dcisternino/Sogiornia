@extends('layouts.guest')

@section('title', $section['title'].' · '.$propertyName)

{{-- Dettaglio coerente con la scheda iniziale della guida. --}}
@section('content')
    <div class="guest-content guest-detail">
        {{-- Ritorno alla guida con lo stesso link riservato del soggiorno. --}}
        <a class="guest-back-link" href="{{ $homeUrl }}">← {{ __('guest.back_to_guide') }}</a>

        <article>
            {{-- Titolo e contesto della struttura. --}}
            <p class="guest-kicker">{{ $propertyName }}</p>
            <h1>{{ $section['title'] }}</h1>
            @if($section['is_fallback'] ?? false)<p class="guest-original-note" role="note">{{ __('guest.original_content') }}</p>@endif

            {{-- Testo escapato e immagine protetta dalla validità del soggiorno. --}}
            <div class="preserve-lines section-content">{{ $section['content'] }}</div>
            @if ($section['image_url'] ?? null)
                <img class="section-image mt-4" src="{{ $section['image_url'] }}" alt="{{ $section['image_alt'] ?: $section['title'] }}" loading="lazy">
            @endif

            {{-- Azioni costruite esclusivamente con collegamenti e numeri validati. --}}
            <div class="guest-detail-actions">
                @if ($section['link_url'] ?? null)
                    <a class="btn btn-primary" href="{{ $section['link_url'] }}" target="_blank" rel="noopener noreferrer">{{ $section['link_label'] ?: __('guest.open_link') }} ↗</a>
                @endif
                @if ($section['phone'] ?? null)
                    <a class="btn btn-outline-secondary" href="tel:{{ preg_replace('/[^+0-9]/', '', $section['phone']) }}">{{ __('guest.call', ['phone' => $section['phone']]) }}</a>
                @endif
            </div>
            {{-- Contenuti attivi nell'ordine scelto dall'host, raggruppati per titolo consecutivo. --}}
            <div class="guest-blocks">
                @foreach(($section['blocks'] ?? []) as $block)
                    @if($block['group_title'] && ($loop->first || $block['group_title'] !== $section['blocks'][$loop->index - 1]['group_title']))
                        <h2 class="guest-block-group">{{ $block['group_title'] }}</h2>
                    @endif
                    <section class="guest-block">
                        @if($block['image_url'])<img class="guest-block-photo" src="{{ $block['image_url'] }}" alt="{{ $block['image_alt'] ?: $block['title'] }}" loading="lazy">@endif
                        <div class="guest-block-body">
                            @if($block['group_title'])<h3>{{ $block['title'] }}</h3>@else<h2>{{ $block['title'] }}</h2>@endif
                            @if($block['is_fallback'])<p class="guest-original-note">{{ __('guest.original_content') }}</p>@endif
                            @if($block['content'])<div class="preserve-lines">{{ $block['content'] }}</div>@endif
                            @if($block['address'])<p class="small mt-3 mb-0">{{ $block['address'] }}</p>@endif
                            <div class="guest-detail-actions">
                                @if($block['link_url'])<a class="btn btn-primary" href="{{ $block['link_url'] }}" target="_blank" rel="noopener noreferrer">{{ $block['link_label'] ?: __('guest.open_link') }} ↗</a>@endif
                                @if($block['phone'])<a class="btn btn-outline-secondary" href="tel:{{ preg_replace('/[^+0-9]/', '', $block['phone']) }}">{{ __('guest.call', ['phone' => $block['phone']]) }}</a>@endif
                            </div>
                        </div>
                    </section>
                @endforeach
            </div>
        </article>
    </div>
@endsection
