@props(['payload'])
{{-- Rende i dati versionati come testo escapato, incluse traduzioni, blocchi e assegnazioni. --}}
<h3 class="h5">{{ $payload['property']['name'] }}</h3>
<p>{{ $payload['property']['city'] }} · {{ $payload['property']['address'] }}</p>
<p style="white-space: pre-wrap">{{ $payload['property']['description'] }}</p>
<p style="white-space: pre-wrap">{{ $payload['property']['welcome_message'] }}</p>
@if($payload['property']['image_path'])<p class="small">Foto struttura: {{ basename($payload['property']['image_path']) }}</p>@endif
@foreach($payload['property']['translations'] as $translation)<p><strong>{{ strtoupper($translation['locale']) }}</strong> {{ $translation['welcome_message'] }}</p>@endforeach
<h3 class="h5 mt-4">Stanze e visibilità</h3>
@forelse($payload['rooms'] as $room)
<div class="border-bottom py-2"><strong>{{ $room['name'] }}</strong><p class="mb-1">{{ $room['description'] }}</p>
@if($room['image_path'])<p class="small mb-1">Foto: {{ basename($room['image_path']) }}</p>@endif
<p class="small mb-0">Sezioni visibili: @forelse($payload['sections'] as $section)@if($section['is_active'] && ($section['is_shared'] || in_array($section['id'], $room['section_ids'], true)))<span class="badge badge-soft">{{ $section['title'] }}</span> @endif @empty Nessuna sezione configurata @endforelse</p></div>
@empty<p>Nessuna stanza.</p>@endforelse
<h3 class="h5 mt-4">Sezioni e contenuti</h3>
@forelse($payload['sections'] as $section)
<article class="border rounded p-3 mb-3">
    <h4 class="h5">{{ $section['title'] }} <span class="badge badge-soft">{{ $section['is_active'] ? 'Attiva' : 'Disattiva' }}</span> @if($section['is_shared'])<span class="badge badge-soft">Condivisa</span>@endif</h4>
    <p class="small text-muted-custom">{{ $section['slug'] }}</p>
    <p style="white-space: pre-wrap">{{ $section['content'] }}</p>
    @foreach(['image_path' => 'Foto', 'image_alt' => 'Descrizione foto', 'link_url' => 'Link', 'link_label' => 'Etichetta link', 'phone' => 'Telefono'] as $field => $label)@if(filled($section[$field]))<p class="small mb-1"><strong>{{ $label }}:</strong> {{ $field === 'image_path' ? basename($section[$field]) : $section[$field] }}</p>@endif @endforeach
    @foreach($section['translations'] as $translation)<details class="mt-2"><summary>Traduzione {{ strtoupper($translation['locale']) }}</summary>@foreach($translation as $field => $value)@if($field !== 'locale' && filled($value))<p style="white-space: pre-wrap"><strong>{{ $field }}:</strong> {{ $value }}</p>@endif @endforeach</details>@endforeach
    @foreach($section['blocks'] as $block)
    <div class="border-top mt-3 pt-3"><h5 class="h6">{{ $block['title'] ?: 'Contenuto senza titolo' }} · {{ $block['type'] }} · {{ $block['is_active'] ? 'Attivo' : 'Disattivo' }}</h5>
        @foreach(['group_title', 'content', 'image_path', 'image_alt', 'link_url', 'link_label', 'phone', 'address'] as $field)@if(filled($block[$field]))<p style="white-space: pre-wrap"><strong>{{ $field }}:</strong> {{ $field === 'image_path' ? basename($block[$field]) : $block[$field] }}</p>@endif @endforeach
        @foreach($block['translations'] as $translation)<details><summary>Traduzione {{ strtoupper($translation['locale']) }}</summary>@foreach($translation as $field => $value)@if($field !== 'locale' && filled($value))<p style="white-space: pre-wrap"><strong>{{ $field }}:</strong> {{ $value }}</p>@endif @endforeach</details>@endforeach
    </div>@endforeach
</article>
@empty<p>Nessuna sezione.</p>@endforelse
