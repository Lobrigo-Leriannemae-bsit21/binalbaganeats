@props(['name' => 'quantity', 'value' => 1, 'min' => 1, 'max' => 20])

<div class="input-group be-qty" data-qty>
    <button class="btn btn-outline-secondary" type="button" data-qty-minus aria-label="Decrease quantity">
        <i class="bi bi-dash"></i>
    </button>
    <input type="number" name="{{ $name }}" value="{{ $value }}"
           min="{{ $min }}" max="{{ $max }}" inputmode="numeric"
           class="form-control" aria-label="Quantity">
    <button class="btn btn-outline-secondary" type="button" data-qty-plus aria-label="Increase quantity">
        <i class="bi bi-plus"></i>
    </button>
</div>