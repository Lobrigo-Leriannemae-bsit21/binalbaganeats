@php
    $links = [
        ['account.profile', 'bi-person', 'Profile'],
        ['account.address', 'bi-geo-alt', 'Delivery address'],
        ['account.password', 'bi-shield-lock', 'Change password'],
        ['account.settings', 'bi-gear', 'Account settings'],
    ];
@endphp

<nav class="be-account-nav be-card p-2" aria-label="Account">
    @foreach ($links as [$routeName, $icon, $label])
        <a href="{{ route($routeName) }}"
           class="{{ request()->routeIs($routeName.'*') ? 'active' : '' }}"
           @if (request()->routeIs($routeName.'*')) aria-current="page" @endif>
            <i class="bi {{ $icon }}"></i> <span>{{ $label }}</span>
        </a>
    @endforeach
</nav>