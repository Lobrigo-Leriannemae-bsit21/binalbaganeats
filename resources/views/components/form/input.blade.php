@props([
    'name',
    'label',
    'type' => 'text',
    'value' => null,
    'required' => false,
    'help' => null,
])

<div class="mb-3">
    <label for="{{ $name }}" class="form-label fw-semibold">
        {{ $label }}@if ($required)<span class="text-danger"> *</span>@endif
    </label>

    @if ($type === 'password')
        <div class="input-group">
            <input type="password" id="{{ $name }}" name="{{ $name }}"
                   @if ($required) required aria-required="true" @endif
                   {{ $attributes->class(['form-control', 'is-invalid' => $errors->has($name)]) }}>
            <button class="btn btn-outline-secondary" type="button"
                    data-toggle-password="{{ $name }}" aria-label="Show or hide password">
                <i class="bi bi-eye"></i>
            </button>
        </div>
    @else
        <input type="{{ $type }}" id="{{ $name }}" name="{{ $name }}"
               value="{{ old($name, $value) }}"
               @if ($required) required aria-required="true" @endif
               {{ $attributes->class(['form-control', 'is-invalid' => $errors->has($name)]) }}>
    @endif

    @if ($help)
        <div class="form-text">{{ $help }}</div>
    @endif

    @error($name)
        <div class="invalid-feedback d-block">{{ $message }}</div>
    @enderror
</div>