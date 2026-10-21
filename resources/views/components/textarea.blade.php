@props(['name', 'label', 'value' => '', 'required' => false, 'help' => null, 'rows' => 5])
@php($inputValue = old($name, $value))

{{-- Campo di testo esteso; i contenuti vengono sempre mostrati con escaping. --}}
<div class="mb-4">
    <label class="form-label" for="{{ $name }}">{{ $label }} @if ($required)<span aria-hidden="true">*</span>@endif</label>
    <textarea id="{{ $name }}" name="{{ $name }}" rows="{{ $rows }}" {{ $attributes->class(['form-control', 'is-invalid' => $errors->has($name)]) }}
        @required($required)
        @if ($help || $errors->has($name)) aria-describedby="{{ $name }}-feedback" @endif
        @if ($errors->has($name)) aria-invalid="true" @endif>{{ is_scalar($inputValue) ? $inputValue : '' }}</textarea>
    @if ($errors->has($name))
        <div class="invalid-feedback" id="{{ $name }}-feedback">{{ $errors->first($name) }}</div>
    @elseif ($help)
        <div class="form-text" id="{{ $name }}-feedback">{{ $help }}</div>
    @endif
</div>
