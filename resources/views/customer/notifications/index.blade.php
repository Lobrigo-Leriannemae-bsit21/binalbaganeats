@extends('layouts.app')

@section('title', 'Notifications')

@section('content')
<div class="container py-4 py-lg-5">
    <div class="row justify-content-center">
        <div class="col-lg-8">

            <div class="d-flex flex-wrap justify-content-between align-items-center gap-2 mb-4">
                <h1 class="h3 mb-0">Notifications</h1>

                @if ($unreadCount > 0)
                    <form method="POST" action="{{ route('notifications.read-all') }}">
                        @csrf
                        <button type="submit" class="btn btn-outline-primary btn-sm">
                            <i class="bi bi-check2-all"></i> Mark all as read
                        </button>
                    </form>
                @endif
            </div>

            <div class="be-chip-row mb-4">
                <a href="{{ route('notifications.index') }}" class="be-cat-chip {{ $filter === 'all' ? 'active' : '' }}">All</a>
                <a href="{{ route('notifications.index', ['filter' => 'unread']) }}" class="be-cat-chip {{ $filter === 'unread' ? 'active' : '' }}">
                    Unread ({{ $unreadCount }})
                </a>
            </div>

            @if ($notifications->isEmpty())
                @if ($filter === 'unread')
                    <x-empty-state icon="bi-check2-circle" title="You’re all caught up"
                                   text="You have no unread notifications.">
                        <a href="{{ route('notifications.index') }}" class="btn btn-primary">Show all notifications</a>
                    </x-empty-state>
                @else
                    <x-empty-state icon="bi-bell-slash" title="No notifications yet"
                                   text="Updates about your orders, payments, points and Buy-for-Me requests will appear here." />
                @endif
            @else
                <div class="be-card overflow-hidden">
                    @foreach ($notifications as $notification)
                        @php
                            $data = $notification->data;
                            $isUnread = $notification->read_at === null;
                        @endphp

                        <form method="POST" action="{{ route('notifications.read', $notification->id) }}"
                              class="{{ $loop->last ? '' : 'border-bottom' }}">
                            @csrf
                            <button type="submit" class="be-notice {{ $isUnread ? 'unread' : '' }}">
                                <span class="be-notice-icon cat-{{ $data['category'] ?? 'order' }}">
                                    <i class="bi {{ $data['icon'] ?? 'bi-bell' }}"></i>
                                </span>
                                <span class="be-notice-body">
                                    <span class="be-notice-title">{{ $data['title'] ?? 'Notification' }}</span>
                                    <span class="be-notice-text">{{ $data['message'] ?? '' }}</span>
                                    <span class="be-notice-time">{{ $notification->created_at->diffForHumans() }}</span>
                                </span>
                                @if ($isUnread)
                                    <span class="be-notice-dot" aria-label="Unread"></span>
                                @endif
                            </button>
                        </form>
                    @endforeach
                </div>

                <div class="mt-4 d-flex justify-content-center">
                    {{ $notifications->links() }}
                </div>
            @endif
        </div>
    </div>
</div>
@endsection