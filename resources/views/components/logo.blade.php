@props([
    'size'    => 48,      {{-- logo height in pixels --}}
    'text'    => true,    {{-- show the "BinalbaganEats" wordmark --}}
    'tagline' => true,    {{-- show "Food made closer" --}}
    'href'    => '/',
])

<a href="{{ $href }}" class="be-logo text-decoration-none d-inline-flex align-items-center gap-2">
    <img src="{{ asset('images/logo.png') }}"
         alt="BinalbaganEats logo"
         style="height: {{ (int) $size }}px;">

    @if ($text)
        <span class="d-flex flex-column">
            <span class="be-wordmark" style="font-size: {{ round($size * 0.55) }}px;">
                <span class="be-wordmark-a">Binalbagan</span><span class="be-wordmark-b">Eats</span>
            </span>
            @if ($tagline)
                <span class="be-tagline">Food made closer</span>
            @endif
        </span>
    @endif
</a>