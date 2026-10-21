@props(['name', 'label', 'type' => 'text', 'value' => '', 'required' => false, 'help' => null])
@php($inputValue = old($name, $value))

{{-- Campo riutilizzabile con etichetta, aiuto ed errore accessibile. --}}
<div class="mb-4">
    <label class="form-label" for="{{ $name }}">{{ $label }} @if ($required)<span aria-hidden="true">*</span>@endif</label>
    <input id="{{ $name }}" name="{{ $name }}" type="{{ $type }}" value="{{ $type !== 'password' && is_scalar($inputValue) ? $inputValue : '' }}"
        {{ $attributes->class(['form-control', 'is-invalid' => $errors->has($name)]) }}
        @required($required)
        @if ($help || $errors->has($name)) aria-describedby="{{ $name }}-feedback" @endif
        @if ($errors->has($name)) aria-invalid="true" @endif>
    @if ($errors->has($name))
        <div class="invalid-feedback" id="{{ $name }}-feedback">{{ $errors->first($name) }}</div>
    @elseif ($help)
        <div class="form-text" id="{{ $name }}-feedback">{{ $help }}</div>
    @endif
</div>
