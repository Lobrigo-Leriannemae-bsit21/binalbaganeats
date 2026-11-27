@extends('layouts.app')

@section('title', 'My cart')

@section('content')
<div class="container py-4 py-lg-5">

    <h1 class="h3 mb-4">My cart</h1>

    @if ($lines->isEmpty())
        <x-empty-state icon="bi-cart3" title="Your cart is empty"
                       text="Browse local vendors and add something delicious.">
            <a href="{{ route('vendors.index') }}" class="btn btn-primary">Browse vendors</a>
        </x-empty-state>
    @else
        <div class="row g-4">

            {{-- Items --}}
            <div class="col-lg-8">
                <div class="be-card">
                    @if ($vendorProfile)
                        <div class="p-3 border-bottom d-flex justify-content-between align-items-center gap-2">
                            <div>
                                <small class="text-muted">Ordering from</small><br>
                                <a href="{{ route('vendors.show', $vendorProfile) }}" class="fw-semibold">{{ $vendorProfile->business_name }}</a>
                            </div>
                            <span class="be-badge {{ $vendorProfile->is_available ? 'be-badge-success' : 'be-badge-danger' }}">
                                {{ $vendorProfile->is_available ? 'Open' : 'Closed' }}
                            </span>
                        </div>
                    @endif

                    @foreach ($lines as $line)
                        @php($item = $line['item'])

                        <div class="d-flex flex-wrap gap-3 p-3 {{ $loop->last ? '' : 'border-bottom' }}">
                            <a href="{{ route('menu.show', $item) }}">
                                <img src="{{ $item->image_url }}" alt="{{ $item->item_name }}" class="be-cart-img">
                            </a>

                            <div class="flex-grow-1">
                                <a href="{{ route('menu.show', $item) }}" class="fw-semibold text-dark text-decoration-none">{{ $item->item_name }}</a>
                                <div class="small text-muted">₱{{ number_format($item->price, 2) }} each</div>
                                @unless ($line['orderable'])
                                    <span class="be-badge be-badge-danger mt-1">
                                        {{ $item->is_available ? 'Store closed' : 'Unavailable' }}
                                    </span>
                                @endunless
                            </div>

                            <div class="d-flex align-items-center gap-3 ms-auto">
                                <form method="POST" action="{{ route('cart.update', $item) }}" data-autosubmit>
                                    @csrf
                                    @method('PATCH')
                                    <x-quantity-input :value="$line['quantity']" :max="$maxQuantity" />
                                    <noscript><button type="submit" class="btn btn-sm btn-outline-primary mt-1">Update</button></noscript>
                                </form>

                                <div class="fw-bold text-end be-line-total {{ $line['orderable'] ? '' : 'text-decoration-line-through text-muted' }}">
                                    ₱{{ number_format($line['line_total'], 2) }}
                                </div>

                                <form method="POST" action="{{ route('cart.destroy', $item) }}">
                                    @csrf
                                    @method('DELETE')
                                    <button type="submit" class="btn btn-outline-danger btn-sm"
                                            aria-label="Remove {{ $item->item_name }} from cart">
                                        <i class="bi bi-trash"></i>
                                    </button>
                                </form>
                            </div>
                        </div>
                    @endforeach
                </div>

                <form method="POST" action="{{ route('cart.clear') }}" class="mt-3"
                      data-confirm="Remove everything from your cart?">
                    @csrf
                    @method('DELETE')
                    <button type="submit" class="btn btn-link text-danger p-0">
                        <i class="bi bi-x-circle"></i> Clear cart
                    </button>
                </form>
            </div>

            {{-- Summary --}}
            <div class="col-lg-4">
                <div class="be-card p-4 be-sticky-summary">
                    <h2 class="h5 mb-3">Order summary</h2>

                    <div class="d-flex justify-content-between mb-2">
                        <span>Items</span><strong>{{ $itemCount }}</strong>
                    </div>
                    <div class="d-flex justify-content-between mb-3">
                        <span>Subtotal</span><strong>₱{{ number_format($subtotal, 2) }}</strong>
                    </div>

                    <p class="small text-muted">Delivery fee and payment method are added at checkout.</p>

                    @if ($hasUnavailable)
                        <div class="alert alert-warning small">
                            Some items can’t be ordered right now. They are not counted in the subtotal; remove them to continue.
                        </div>
                    @endif

                    @if ($vendorProfile && ! $vendorProfile->is_available)
                        <div class="alert alert-warning small">This store is closed right now, so you can’t check out yet.</div>
                    @endif

                    @if ($vendorProfile && $vendorProfile->is_available && $itemCount > 0 && ! $hasUnavailable)
                        <a href="{{ route('checkout.show') }}" class="btn btn-primary w-100 mb-2">
                            Proceed to checkout <i class="bi bi-arrow-right ms-1"></i>
                        </a>
                    @endif

                    @if ($vendorProfile)
                        <a href="{{ route('vendors.show', $vendorProfile) }}" class="btn btn-outline-primary w-100">
                            Add more items
                        </a>
                    @endif
                </div>
            </div>
        </div>
    @endif
</div>
@endsection