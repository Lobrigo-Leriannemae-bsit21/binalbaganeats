@props(['status' => 'Pending'])

@php
    $normalized = strtolower(trim($status));
    $classes = match (true) {
        in_array($normalized, ['approved', 'delivered', 'completed', 'verified', 'settled', 'active', 'available']) => 'bg-success-subtle text-success border border-success-subtle',
        in_array($normalized, ['pending', 'payment verification', 'awaiting verification', 'refund pending']) => 'bg-warning-subtle text-warning-emphasis border border-warning-subtle',
        in_array($normalized, ['accepted', 'preparing', 'ready for pickup', 'assigned', 'picked up', 'purchasing for buy-for-me']) => 'bg-info-subtle text-info-emphasis border border-info-subtle',
        in_array($normalized, ['rejected', 'declined', 'cancelled', 'unverified', 'flagged', 'unavailable']) => 'bg-danger-subtle text-danger border border-danger-subtle',
        default => 'bg-secondary-subtle text-secondary border border-secondary-subtle',
    };
@endphp

<span {{ $attributes->merge(['class' => "badge rounded-pill px-3 py-1 fw-semibold $classes"]) }}>
    {{ $status }}
</span>