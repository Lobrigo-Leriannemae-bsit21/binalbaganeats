@props(['role' => 'customer'])

@php
    $menus = [
        'customer' => [
            ['label' => 'Dashboard', 'route' => 'customer.dashboard', 'icon' => 'bi-speedometer2'],
            ['label' => 'Browse Vendors', 'route' => 'customer.vendors', 'icon' => 'bi-shop'],
            ['label' => 'Shopping Cart', 'route' => 'customer.cart', 'icon' => 'bi-cart3'],
            ['label' => 'My Orders', 'route' => 'customer.orders', 'icon' => 'bi-receipt'],
            ['label' => 'Order Tracking', 'route' => 'customer.tracking', 'icon' => 'bi-geo-alt'],
            ['label' => 'Suki Points', 'route' => 'customer.suki-points', 'icon' => 'bi-award'],
            ['label' => 'Buy-for-Me', 'route' => 'customer.buy-for-me', 'icon' => 'bi-bag-heart'],
            ['label' => 'Notifications', 'route' => 'customer.notifications', 'icon' => 'bi-bell'],
            ['label' => 'Profile', 'route' => 'customer.profile', 'icon' => 'bi-person'],
        ],
        'vendor' => [
            ['label' => 'Dashboard', 'route' => 'vendor.dashboard', 'icon' => 'bi-speedometer2'],
            ['label' => 'Storefront', 'route' => 'vendor.storefront', 'icon' => 'bi-shop-window'],
            ['label' => 'Menu Management', 'route' => 'vendor.menu', 'icon' => 'bi-journal-text'],
            ['label' => 'Incoming Orders', 'route' => 'vendor.incoming-orders', 'icon' => 'bi-bell-fill'],
            ['label' => 'Order Management', 'route' => 'vendor.orders', 'icon' => 'bi-list-check'],
            ['label' => 'Completed Orders', 'route' => 'vendor.completed-orders', 'icon' => 'bi-check2-circle'],
            ['label' => 'Sales Records', 'route' => 'vendor.sales', 'icon' => 'bi-bar-chart-line'],
            ['label' => 'Earnings & Payouts', 'route' => 'vendor.earnings', 'icon' => 'bi-wallet2'],
            ['label' => 'Notifications', 'route' => 'vendor.notifications', 'icon' => 'bi-bell'],
            ['label' => 'Profile', 'route' => 'vendor.profile', 'icon' => 'bi-person'],
        ],
        'rider' => [
            ['label' => 'Dashboard', 'route' => 'rider.dashboard', 'icon' => 'bi-speedometer2'],
            ['label' => 'Available Jobs', 'route' => 'rider.available-jobs', 'icon' => 'bi-broadcast'],
            ['label' => 'Assigned Deliveries', 'route' => 'rider.assigned', 'icon' => 'bi-scooter'],
            ['label' => 'Delivery Details', 'route' => 'rider.details', 'icon' => 'bi-card-list'],
            ['label' => 'Delivery Status', 'route' => 'rider.status', 'icon' => 'bi-arrow-repeat'],
            ['label' => 'Buy-for-Me Requests', 'route' => 'rider.buy-for-me', 'icon' => 'bi-bag-check'],
            ['label' => 'Delivery Expenses', 'route' => 'rider.expenses', 'icon' => 'bi-cash-stack'],
            ['label' => 'Delivery History', 'route' => 'rider.history', 'icon' => 'bi-clock-history'],
            ['label' => 'Notifications', 'route' => 'rider.notifications', 'icon' => 'bi-bell'],
            ['label' => 'Profile', 'route' => 'rider.profile', 'icon' => 'bi-person'],
        ],
        'admin' => [
            ['label' => 'Dashboard', 'route' => 'admin.dashboard', 'icon' => 'bi-speedometer2'],
            ['label' => 'Account Management', 'route' => 'admin.accounts', 'icon' => 'bi-people'],
            ['label' => 'Pending Approvals', 'route' => 'admin.approvals', 'icon' => 'bi-person-check'],
            ['label' => 'Vendor Management', 'route' => 'admin.vendors', 'icon' => 'bi-shop'],
            ['label' => 'Rider Management', 'route' => 'admin.riders', 'icon' => 'bi-bicycle'],
            ['label' => 'Delivery Zone Management', 'route' => 'admin.zones', 'icon' => 'bi-map'],
            ['label' => 'Delivery Fee Management', 'route' => 'admin.fees', 'icon' => 'bi-tags'],
            ['label' => 'System Monitoring', 'route' => 'admin.monitoring', 'icon' => 'bi-activity'],
            ['label' => 'Activity Logs', 'route' => 'admin.logs', 'icon' => 'bi-terminal'],
            ['label' => 'Transactions', 'route' => 'admin.transactions', 'icon' => 'bi-credit-card'],
            ['label' => 'Reports & Analytics', 'route' => 'admin.reports', 'icon' => 'bi-pie-chart'],
            ['label' => 'Profile', 'route' => 'admin.profile', 'icon' => 'bi-person'],
        ],
    ];

    $items = $menus[$role] ?? $menus['customer'];
@endphp

<aside class="be-sidebar p-3 d-flex flex-column" id="portalSidebar">
    <div class="d-flex align-items-center justify-content-between mb-4 px-2 pt-2">
        <a href="{{ route('home') }}" class="text-decoration-none text-white d-flex align-items-center gap-2">
            <span class="rounded-circle bg-white d-inline-flex align-items-center justify-content-center overflow-hidden" style="width: 44px; height: 44px;">
                <img src="{{ asset('images/binalbaganeats-scooter-mark.png') }}" alt="" class="be-logo-sidebar">
            </span>
            <div>
                <div class="fw-bold lh-1 fs-5">Binalbagan<span style="color: #E38243;">eats</span></div>
                <small class="text-white-50" style="font-size: 0.72rem;">Local food. On the way.</small>
            </div>
        </a>
    </div>

    <nav class="nav flex-column flex-grow-1">
        @foreach ($items as $item)
            <a class="nav-link {{ request()->routeIs($item['route'] . '*') ? 'active' : '' }}"
               href="{{ Route::has($item['route']) ? route($item['route']) : '#' }}">
                <i class="bi {{ $item['icon'] }}"></i>
                <span>{{ $item['label'] }}</span>
            </a>
        @endforeach
    </nav>

    <div class="border-top border-light border-opacity-25 pt-3 mt-3">
        <form method="POST" action="{{ route('logout') }}">
            @csrf
            <button type="submit" class="btn btn-outline-light btn-sm w-100 d-flex align-items-center justify-content-center gap-2">
                <i class="bi bi-box-arrow-left"></i> Logout
            </button>
        </form>
    </div>
</aside>