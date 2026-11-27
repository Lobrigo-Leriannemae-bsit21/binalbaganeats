@props(['item', 'showVendor' => false])

@php
    $orderable = $item->isOrderable();
    $store = $item->vendor?->vendorProfile;
@endphp

<div class="be-card be-food-card h-100 d-flex flex-column overflow-hidden {{ $item->is_available ? '' : 'is-unavailable' }}">
    <a href="{{ route('menu.show', $item) }}" class="ratio ratio-4x3 d-block" tabindex="-1" aria-hidden="true">
        <img src="{{ $item->image_url }}" alt="" loading="lazy">
    </a>

    <div class="p-3 d-flex flex-column flex-grow-1">
        <span class="small text-muted">{{ $item->category }}</span>
        <a href="{{ route('menu.show', $item) }}" class="fw-semibold text-dark text-decoration-none">{{ $item->item_name }}</a>

        @if ($showVendor && $store)
            <span class="small text-muted"><i class="bi bi-shop"></i> {{ $store->business_name }}</span>
        @endif

        <div class="d-flex justify-content-between align-items-center mt-auto pt-3">
            <span class="be-price">₱{{ number_format($item->price, 2) }}</span>

            @if ($orderable)
                <form method="POST" action="{{ route('cart.store') }}">
                    @csrf
                    <input type="hidden" name="menu_item_id" value="{{ $item->id }}">
                    <input type="hidden" name="quantity" value="1">
                    <button type="submit" class="btn btn-primary btn-sm" aria-label="Add {{ $item->item_name }} to cart">
                        <i class="bi bi-plus-lg"></i> Add
                    </button>
                </form>
            @else
                <span class="be-badge be-badge-neutral">{{ $item->is_available ? 'Store closed' : 'Unavailable' }}</span>
            @endif
        </div>
    </div>
</div>