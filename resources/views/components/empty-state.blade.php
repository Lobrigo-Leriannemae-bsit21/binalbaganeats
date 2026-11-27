@props(['icon' => 'bi-inbox', 'title', 'text' => null])

<div class="be-card p-5 text-center">
    <i class="bi {{ $icon }} fs-1 text-muted"></i>
    <h2 class="h5 mt-2">{{ $title }}</h2>
    @if ($text)
        <p class="text-muted mb-3">{{ $text }}</p>
    @endif
    {{ $slot }}
</div>