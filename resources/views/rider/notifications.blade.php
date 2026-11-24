@extends('layouts.portal', ['role' => 'rider'])

@section('title', 'Notifications')
@section('page-title', 'Rider Notifications')
@section('page-subtitle', 'New available orders and errand dispatch alerts')

@section('content')
<div class="be-card p-4">
    <div class="list-group list-group-flush">
        @foreach ($notifications as $notif)
            <div class="list-group-item px-0 py-3 d-flex justify-content-between align-items-start">
                <div>
                    <div class="fw-bold text-be-primary">
                        @if ($notif['unread']) <span class="badge bg-be-accent me-1">New</span> @endif
                        {{ $notif['title'] }}
                    </div>
                    <p class="small text-be-muted mb-0 mt-1">{{ $notif['message'] }}</p>
                </div>
                <small class="text-be-muted">{{ $notif['time'] }}</small>
            </div>
        @endforeach
    </div>
</div>
@endsection