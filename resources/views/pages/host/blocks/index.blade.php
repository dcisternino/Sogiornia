@extends('layouts.app')
@section('title', 'Contenuti · '.$section->title)

{{-- Elenco ordinato dei contenuti della sezione, con visibilità e azioni esplicite. --}}
@section('content')
<div class="container py-5 workspace">
    <x-setup-shortcut :property="$property" />
    <a class="back-link" href="{{ route('host.sections.index', $property) }}">← Sezioni di {{ $property->name }}</a>
    <div class="page-heading mt-4"><div><span class="eyebrow">Contenuti della sezione</span><h1 class="mt-2">{{ $section->title }}</h1><p class="text-muted-custom">Aggiungi contatti, luoghi o informazioni. Gli ospiti li vedranno nell'ordine indicato.</p></div>
        @if(auth()->user()->canWrite())<a class="btn btn-outline-secondary" href="{{ route('host.sections.edit', [$property, $section]) }}">Modifica titolo e introduzione</a>@endif
    </div>
    @if(auth()->user()->canWrite())
        <div class="sg-card mb-4"><h2 class="h5">Aggiungi un contenuto</h2><p class="small text-muted-custom">Ogni contenuto ha titolo, descrizione e traduzioni. Per organizzare più luoghi puoi inserire un titolo di gruppo.</p><div class="d-flex flex-wrap gap-2">
            @foreach(\App\Models\SectionBlock::TYPES as $type => $label)
                <a class="btn btn-outline-secondary" href="{{ route('host.blocks.create', ['property' => $property, 'section' => $section, 'type' => $type]) }}">+ {{ $label }}</a>
            @endforeach
        </div></div>
    @endif
    <div class="d-grid gap-3">
        @forelse($blocks as $block)
            <article class="sg-card">
                <div class="d-flex flex-wrap justify-content-between align-items-start gap-3"><div><span class="badge badge-soft">{{ \App\Models\SectionBlock::TYPES[$block->type] }}</span> <span class="badge {{ $block->is_active ? 'badge-soft' : 'text-bg-secondary' }}">{{ $block->is_active ? 'Attivo' : 'Nascosto' }}</span>
                    @if($block->group_title)<p class="small mt-2 mb-0">{{ $block->group_title }}</p>@endif
                    <h2 class="h5 mt-2">{{ $block->title }}</h2><p class="small text-muted-custom mb-0">{{ Str::limit($block->content, 180) }}</p>
                    @if($block->phone)<p class="small mt-2 mb-0">Telefono: {{ $block->phone }}</p>@endif
                    @if($block->address)<p class="small mt-2 mb-0">{{ $block->address }}</p>@endif
                </div>
                @if($block->image_path)<img class="block-management-photo" src="{{ route('host.blocks.image', [$property, $section, $block]) }}" alt="{{ $block->image_alt ?: $block->title }}" loading="lazy">@endif</div>
                @if(auth()->user()->canWrite())
                    <div class="d-flex flex-wrap gap-2 mt-3">
                        <a class="btn btn-sm btn-outline-secondary" href="{{ route('host.blocks.edit', [$property, $section, $block]) }}">Modifica<span class="visually-hidden"> {{ $block->title }}</span></a>
                        @foreach(['up' => '↑ Sposta su', 'down' => '↓ Sposta giù', 'toggle' => ($block->is_active ? 'Disattiva' : 'Attiva')] as $action => $label)
                            <form method="POST" action="{{ route('host.blocks.order', [$property, $section, $block]) }}">@csrf @method('PATCH')<input type="hidden" name="action" value="{{ $action }}"><button class="btn btn-sm btn-outline-secondary" type="submit" aria-label="{{ $label }}: {{ $block->title }}" @disabled(($action === 'up' && $blocks->currentPage() === 1 && $loop->parent->first) || ($action === 'down' && !$blocks->hasMorePages() && $loop->parent->last))>{{ $label }}</button></form>
                        @endforeach
                        <form method="POST" action="{{ route('host.blocks.destroy', [$property, $section, $block]) }}" data-confirm="Eliminare definitivamente questo contenuto dalla sezione?"><input type="hidden" name="_method" value="DELETE">@csrf<button class="btn btn-sm btn-outline-danger" type="submit">Elimina<span class="visually-hidden"> {{ $block->title }}</span></button></form>
                    </div>
                @endif
            </article>
        @empty
            <div class="sg-card empty-state"><h2 class="h4">La sezione è pronta per i tuoi contenuti</h2><p class="mb-0 text-muted-custom">Per “Numeri utili” aggiungi un contatto per ciascun numero. Per “Cosa vedere” aggiungi un luogo per ogni posto consigliato.</p></div>
        @endforelse
    </div>
    <div class="mt-4">{{ $blocks->links() }}</div>
</div>
@endsection
