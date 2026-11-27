@foreach (['success' => 'success', 'error' => 'danger', 'status' => 'info'] as $key => $type)
    @if (session($key))
        <div class="container mt-3">
            <div class="alert alert-{{ $type }} alert-dismissible fade show mb-0" role="alert">
                {{ session($key) }}
                <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
            </div>
        </div>
    @endif
@endforeach

{{-- Validation errors from the cart forms (named "cart" error bag) --}}
@if ($errors->getBag('cart')->any())
    <div class="container mt-3">
        <div class="alert alert-danger alert-dismissible fade show mb-0" role="alert">
            {{ $errors->getBag('cart')->first() }}
            <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
        </div>
    </div>
@endif