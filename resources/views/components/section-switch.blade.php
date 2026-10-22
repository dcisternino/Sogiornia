@props(['property', 'section', 'setting', 'label', 'checked'])
{{-- Interruttore accessibile con salvataggio immediato e alternativa senza JavaScript. --}}
<form {{ $attributes->class(['section-switch']) }} method="POST" action="{{ $setupNavigation->url('host.sections.settings', [$property, $section]) }}">
    @csrf <x-onboarding-context /> @method('PATCH')
    <input type="hidden" name="setting" value="{{ $setting }}"><input type="hidden" name="value" value="0">
    <div class="form-check form-switch mb-0">
        <input class="form-check-input" role="switch" type="checkbox" id="{{ $setting }}-{{ $section->id }}" name="value" value="1" @checked($checked) @disabled(!auth()->user()->canWrite()) data-submit-on-change>
        <label class="form-check-label" for="{{ $setting }}-{{ $section->id }}">{{ $label }}<span class="visually-hidden">: {{ $section->title }}</span></label>
    </div>
    @if(auth()->user()->canWrite())<noscript><button class="btn btn-sm btn-outline-secondary mt-2" type="submit">Salva {{ $label }}</button></noscript>@endif
</form>
