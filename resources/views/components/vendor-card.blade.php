@props(['vendor'])

<a href="{{ route('vendors.show', $vendor) }}"
   class="be-card be-vendor-card d-block h-100 text-decoration-none overflow-hidden {{ $vendor->is_available ? '' : 'is-closed' }}">
    <div class="ratio ratio-16x9">
        <img src="{{ $vendor->image_url }}" alt="{{ $vendor->business_name }}" loading="lazy">
    </div>
    <div class="p-3">
        <div class="d-flex justify-content-between align-items-start gap-2 mb-1">
            <h3 class="h6 mb-0 text-dark">{{ $vendor->business_name }}</h3>
            <span class="be-badge {{ $vendor->is_available ? 'be-badge-success' : 'be-badge-danger' }}">
                {{ $vendor->is_available ? 'Open' : 'Closed' }}
            </span>
        </div>

        @if ($vendor->description)
            <p class="small text-muted mb-2">{{ \Illuminate\Support\Str::limit($vendor->description, 80) }}</p>
        @endif

        <div class="small text-muted d-flex flex-wrap gap-3">
            <span><i class="bi bi-clock"></i> ~{{ $vendor->estimated_prep_time }} min prep</span>
            @isset($vendor->available_items_count)
                <span><i class="bi bi-egg-fried"></i> {{ $vendor->available_items_count }} {{ \Illuminate\Support\Str::plural('item', $vendor->available_items_count) }}</span>
            @endisset
        </div>
    </div>
</a>